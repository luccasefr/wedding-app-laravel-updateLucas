<?php

use App\GiftList;
use Faker\Generator as Faker;

$factory->define(GiftList::class, function (Faker $faker) {
    return [
        'name'=>$faker->name,
        'link'=>'https://link.com.br'
    ];
});
