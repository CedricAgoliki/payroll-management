<?php

use Faker\Generator as Faker;

$factory->define(App\Employee::class, function (Faker $faker) {
    return [
	'first_name' => $faker->firstName,
	'last_name' => $faker->lastName,
	'base_salary' => $faker->numberBetween($min=50000, $max=100000),
	'leaves_per_month' => $faker->numberBetween($min=1, $max=3),
	'dependants' => $faker->numberBetween($min=0, $max=10)
    ];
});
