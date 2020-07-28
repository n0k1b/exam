<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class otp_check extends Model
{
    //
    protected $table = 'otp_check';
     protected $fillable = [
        'msisdn','otp'
    ];
}
