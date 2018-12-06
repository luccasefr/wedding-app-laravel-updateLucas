<?php

use App\Song;
use Faker\Generator as Faker;

$factory->define(Song::class, function (Faker $faker) {
    return [
        'name'=>$faker->text(190),
        'artist'=>$faker->text(190),
        'user_id'=>$faker->numberBetween(1,100),
        'guest_id'=>$faker->numberBetween(1,100)
    ];
});
