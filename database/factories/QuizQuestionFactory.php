<?php

use App\QuizQuestion;
use Faker\Generator as Faker;

$factory->define(QuizQuestion::class, function (Faker $faker) {
    return [
        'question'=>$faker->text(190),
        'correct_answer'=>$faker->text(190),
        'wrong_answer_1'=>$faker->text(190),
        'wrong_answer_2'=>$faker->text(190),
        'wrong_answer_3'=>$faker->text(190),
        'user_id'=>$faker->numberBetween(1,100)
    ];
});
