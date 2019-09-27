<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\NewsPost;
use Faker\Generator as Faker;

$factory->define(NewsPost::class, function (Faker $faker) {
    $subpage = factory('App\Subpage')->create();
    return [
        'title' => $faker->sentence,
        'body' => $faker->paragraph,
        'subpage_id' => $subpage->id
    ];
});
