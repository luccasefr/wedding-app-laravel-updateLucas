<?php

use Faker\Generator as Faker;
use App\ChromaImage;

$factory->define(ChromaImage::class, function (Faker $faker) {
    return [
        'img_url'=>$faker->imageUrl(),
        'user_id'=>$faker->numberBetween(0,100)
    ];
});
