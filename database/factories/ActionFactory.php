<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Action::class, function (Faker $faker) {
    $expense = $faker->dateTimeBetween('now','+2 years');
    $notify = $faker->dateTimeBetween('now','+2 years');
    return [
        'title'=>$faker->sentence(),
        'expense'=>true,
        'expense_value'=>$faker->randomNumber(),
        'expense_date'=>$expense->format('d/m/Y'),
        'notify_guests'=>true,
        'notify_date_from'=>$notify->format('d/m/Y'),
        'notify_date_to'=>$notify->format('d/m/Y'),
        'message'=>$faker->text(190),
        'user_id'=>$faker->randomDigit
    ];
});
