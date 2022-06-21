<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiblMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
         Auth::shouldUse('api');
         if(Auth::check() && Auth::user()->sibl())
        {
            return $next($request);
        }
        return redirect()->route('login')->with('danger', 'Unauthorized');
    }
}
