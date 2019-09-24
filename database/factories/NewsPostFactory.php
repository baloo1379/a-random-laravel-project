<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\NewsPost;
use Faker\Generator as Faker;

$factory->define(NewsPost::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'body' => $faker->paragraph
    ];
});
