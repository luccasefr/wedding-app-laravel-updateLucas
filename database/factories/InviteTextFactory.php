<?php

use App\InviteText;
use Faker\Generator as Faker;

$factory->define(InviteText::class, function (Faker $faker) {
    return [
        'text'=>$faker->text(150),
        'width'=>$faker->numberBetween(0,1000),
        'height'=>$faker->numberBetween(0,1000),
        'x'=>$faker->numberBetween(0,1000),
        'y'=>$faker->numberBetween(0,1000),
        'invite_id'=>$faker->numberBetween(0,1000),
        'layer'=>$faker->numberBetween(0,1000),
        'font_id'=>$faker->numberBetween(0,1000),
        'font_size'=>$faker->numberBetween(0,100),
        'hexColor'=>'#000000'
    ];
});
