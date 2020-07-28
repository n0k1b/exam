<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marks_store extends Model
{
    //
     protected $fillable = [
        'user_id','question_answer','exam_date','subject_id','question_id','answer_verdict'
    ];
}
