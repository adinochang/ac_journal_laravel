<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Question;
use App\Models\Entry;
use App\Models\Answer;


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
        factory(Question::class)->create();
        factory(Question::class)->create();


        // Create 30 entries
        $created_at = Carbon::now()->subDays(120);

        for($count = 1; $count <= 30; $count++)
        {
            $created_at->addDay(random_int(1, 7));

            $entry = factory(Entry::class)->create(['created_at' => $created_at->unix()]);

            factory(Answer::class)->create(['entry_id' => $entry->id, 'question_id' => 1, 'created_at' => $created_at->unix()]);
            factory(Answer::class)->create(['entry_id' => $entry->id, 'question_id' => 2, 'created_at' => $created_at->unix()]);
        }
    }
}
