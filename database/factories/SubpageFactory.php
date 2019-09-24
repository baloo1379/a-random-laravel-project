<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Subpage;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Subpage::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence
    ];
});
