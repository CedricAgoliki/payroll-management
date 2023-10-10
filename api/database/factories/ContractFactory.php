<?php

use Faker\Generator as Faker;

$factory->define(App\Contract::class, function (Faker $faker) {
    return [
	'start' => $faker->date($format='Y-m-d'),
	'end' => $faker->date($format='Y-m-d'),
	'commentary' => $faker->realText($maxNbChars=200),
	'ended' => false
    ];
});
