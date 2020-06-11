<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create two dummy questions
        factory(App\Question::class)->create();
        factory(App\Question::class)->create();

        // Create two sample journal entries
        factory(App\Answer::class)->create();
        factory(App\Answer::class)->create();
    }
}
