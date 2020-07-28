<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class challenge_friend_question extends Model
{
    //
     protected $fillable = [
        'user_id','question_category','code'
    ];
}
