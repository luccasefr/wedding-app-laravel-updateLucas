<?php

use Faker\Generator as Faker;
use App\SensibleWord;

$factory->define(SensibleWord::class, function (Faker $faker) {
    return [
        'word'=>$faker->word,
        'user_id'=>$faker->randomNumber()
    ];
});
