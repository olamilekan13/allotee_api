<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class coverPlan extends Model
{
    use HasFactory,Notifiable;


    protected $fillable = [
        'cover_type',
          'cover_flat',
           'cover_price',
           
    ];
}
