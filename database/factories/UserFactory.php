<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$jfTrMt/wDrIXsxT1G.yiHO52tchE9amW.3.u2usl8XMt4zT6ndWgq',
        //'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'role' => 'user',
    ];
});
