<?php

use Faker\Generator as Faker;
use App\InviteImage;

$factory->define(InviteImage::class, function (Faker $faker) {
    return [
        'image_url'=>$faker->imageUrl(),
        'width'=>$faker->numberBetween(0,1000),
        'height'=>$faker->numberBetween(0,1000),
        'x'=>$faker->numberBetween(0,1000),
        'y'=>$faker->numberBetween(0,1000),
        'invite_id'=>$faker->numberBetween(0,1000),
        'layer'=>$faker->numberBetween(0,1000),
    ];
});
