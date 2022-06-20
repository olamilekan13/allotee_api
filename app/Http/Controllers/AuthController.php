<?php

namespace App\Http\Controllers;


use App\Models\User;
use Validator;
use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\PasswordReset;
use App\Mail\ResetPasswordEmail;

class AuthController extends Controller
{
    

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);
        return response()->json([ 'valid' => auth()->check() ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        try
        {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            // if ($validator->fails()) {
            //     return response()->json($validator->errors(), 422);
            // }

            if (! $token = auth('api')->attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->createNewToken($token);
        } catch (ValidationException $e) {
            return response()->json([
                "error" => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        try{
            $this->validate($request, [
                'owners_name' => 'string|between:2,100',
                'occupiers_name' => 'string|between:2,100',
                'phone' => 'string',
                'location' => 'string',
                'block_number' => 'string',
                'flat_number' => 'string',
                'img' => '',
                'email' => 'string|email|max:100|unique:users',
                'password' => 'string|confirmed|min:6',
            ]);

          

            if($request->hasFile("img"))
            {
                $img_path = public_path("images/user/");
                $img = $request->file("img");
                $img_name = Str::random(16).'.'.$img->getClientOriginalExtension();
                if($img->move($img_path, $img_name))
                {
                    $imgname = $img_name;
                }
            }else{
                $imgname = null;
            }

            

            $user = User::create([
                'owners_name' => $request->owners_name,
                'occupiers_name' => $request->occupiers_name,
                'phone' => $request->phone,
                'location' => $request->location,
                'block_number' => $request->block_number,
                'flat_number' => $request->flat_number,
                'img' => $request->img,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'img' => $imgname,
            ]);

    

            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 200);  // changed to 200
        } catch (ValidationException $e) {
            return response()->json([
                "message" => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }


    // }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout() {

      try{

      
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

catch (\Exception $e) {
        // Return Json Response
        return response()->json([
            'message' => $e->getMessage()
        ],500);
    }
}
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        try{
        return $this->createNewToken(auth()->refresh());
        }
        catch (\Exception $e) {
        // Return Json Response
        return response()->json([
            'message' => $e->getMessage()
        ],500);
    }
    }
    














/**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 6000,
            'user' => auth('api')->user()
        ]);
    }






}
