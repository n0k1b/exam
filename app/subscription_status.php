<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class subscription_status extends Model
{
    //
    protected $table = 'subscription_status';
     protected $fillable = [
        'mobile','status','timestamp','medium'
    ];
}
