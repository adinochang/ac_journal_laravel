<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Entry;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Entry::class, function (Faker $faker, $attributes) {
    $created_at = Carbon::createFromTimestamp($attributes['created_at']);

    return [
        'created_at' => $created_at,
        'updated_at' => $created_at,
    ];
});
