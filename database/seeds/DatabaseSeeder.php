<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;


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


        // Create 30 entries
        $created_at = Carbon::now()->subDays(60);

        for($count = 1; $count <= 30; $count++)
        {
            $created_at->addDay(random_int(1, 7));

            $entry = factory(App\Entry::class)->create(['created_at' => $created_at->unix()]);

            factory(App\Answer::class)->create(['entry_id' => $entry->id, 'question_id' => 1, 'created_at' => $created_at->unix()]);
            factory(App\Answer::class)->create(['entry_id' => $entry->id, 'question_id' => 2, 'created_at' => $created_at->unix()]);
        }
    }
}
