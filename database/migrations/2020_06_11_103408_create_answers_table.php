<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_journal_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entry_id');
            $table->unsignedBigInteger('question_id');
            $table->mediumText('answer_text');
            $table->timestamps();

            $table->foreign('entry_id')->references('id')->on('ac_journal_entries');
            $table->foreign('question_id')->references('id')->on('ac_journal_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ac_journal_answers');
    }
}
