<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CastMember;
use Faker\Generator as Faker;

$factory->define(CastMember::class, function (Faker $faker) {
    $types = CastMember::getTypeArray();
    return [
        'name' => $faker->lastName,
        'type' => $types[array_rand($types)]
    ];
});
