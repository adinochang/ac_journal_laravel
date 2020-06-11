<?php

/** @var Factory $factory */

use App\Answer;
use App\Entry;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Answer::class, function (Faker $faker) {
    return [
        'entry_id' => factory(Entry::class),
        'question_id' => random_int(1, 2),
        'answer_text' => $faker->paragraph,
    ];
});
