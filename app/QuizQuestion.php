<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['question','correct_answer','wrong_answer_1','wrong_answer_2','wrong_answer_3','user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
