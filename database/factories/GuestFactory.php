<?php

use Faker\Generator as Faker;

$factory->define(App\Guest::class, function (Faker $faker) {
    return [
        'id'=>$faker->unique()->bothify('??######'),
        'user_id'=>$faker->randomDigit,
        'name'=>$faker->name,
        'email'=>$faker->unique()->safeEmail,
        'phone'=>$faker->randomNumber(9),
        'age'=>$faker->randomNumber(2),
        'confirmed'=>$faker->numberBetween(0,1),
        'profile_img'=>'profile.jpg',
        'photo1_url'=>'profile.jpg',
        'photo2_url'=>'profile.jpg',
        'photo3_url'=>'profile.jpg',
        'about'=>$faker->text(100),
        'gender_id'=>$faker->numberBetween(1,2),
        'want_gender_id'=>$faker->numberBetween(1,2),
        'is_on_singles_meeting'=>$faker->numberBetween(0,1),
        'fcm_device_token'=>"dZOkgEuyYq8:APA91bFX00gUFimqskoiK9Rg1vF3iLtajfhsRJ1-Kqc1XKHDR7b6gf6Mvq6nvuTUoZL6rESWzJfVcs7mJVgMYCXt9s4ptzpovP3xTK3Frrqf9fMzbtz5G24xHQcEXlTBOtmTu-olUYwF",
    ];
});
