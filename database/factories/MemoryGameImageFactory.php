<?php

use Faker\Generator as Faker;
use App\MemoryGameImage;

$factory->define(MemoryGameImage::class, function (Faker $faker) {
    return [
        'img_url'=>$faker->imageUrl(),
        'user_id'=>$faker->numberBetween(0,100)
    ];
});
