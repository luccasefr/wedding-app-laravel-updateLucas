<?php

use App\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'text'=>$faker->text(249),
        'guest_id'=>'TT'.$faker->randomNumber(6),
        'image_url'=>$faker->imageUrl(),
        'aproved'=>$faker->boolean()
    ];
});
