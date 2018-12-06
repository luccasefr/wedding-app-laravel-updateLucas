<?php

use Faker\Generator as Faker;
use App\PuzzleImage;

$factory->define(PuzzleImage::class, function (Faker $faker) {
    return [
        'img_url'=>$faker->imageUrl(),
        'user_id'=>$faker->numberBetween(0,100)
    ];
});
