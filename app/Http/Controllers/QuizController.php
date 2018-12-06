<?php

namespace App\Http\Controllers;

use App\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function create()
    {
      request()->validate([
        'question'=>'required',
        'correct_answer'=>'required',
        'wrong_answer_1'=>'required',
        'wrong_answer_2'=>'required',
        'wrong_answer_3'=>'required'
      ]);

      $data = ['user_id'=>Auth::user()->id]+request()->all();

      return QuizQuestion::create($data);
    }

    public function delete(QuizQuestion $question)
    {
        if($question->user->id!=Auth::user()->id){
            return response(['message'=>'you are not authorized to delete this quiz question'],401);
        }

        $question->delete();
        return response(['message'=>'quiz question delete successfuly'],200);
    }
}
