<?php

use App\Event;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    $date = $faker->dateTimeBetween('now','+2 years');
    return [
        'name'=>$faker->text(50),
        'date'=>$date->format('d/m/Y H:i'),
        'address_id'=>$faker->randomNumber(),
        'user_id'=>$faker->randomNumber()
    ];
});
