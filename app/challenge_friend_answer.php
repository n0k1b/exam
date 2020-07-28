<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class challenge_friend_answer extends Model
{ 

    
     protected $fillable = [
        'user_id','question_answer','code','challenge_date','score'
    ];
}
