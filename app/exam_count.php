<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class exam_count extends Model
{
    //
     protected $table = 'exam_count';
     protected $fillable = [
        'user_id','exam_count','exam_date',
    ];
}
