<?php

/** @var Factory $factory */

use App\Answer;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Answer::class, function (Faker $faker, $attributes) {
    $created_at = Carbon::createFromTimestamp($attributes['created_at']);

    return [
        'entry_id' => $attributes['entry_id'],
        'question_id' => $attributes['question_id'],
        'answer_text' => $faker->paragraph((random_int(3, 15))),
        'created_at' => $created_at,
        'updated_at' => $created_at,
    ];
});
