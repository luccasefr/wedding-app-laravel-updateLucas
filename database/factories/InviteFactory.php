<?php

use App\Invite;
use Faker\Generator as Faker;

$factory->define(Invite::class, function (Faker $faker) {
    return [
        'bg_url'=>$faker->imageUrl(),
    ];
});
