<?php
namespace App\Repositories;

use App\Models\Answer;
use Illuminate\Http\Request;


class AnswerRepository
{
    protected $answerModel;


    /**
     * @param Answer|null $answerModel
     */
    public function __construct(Answer $answerModel = null)
    {
        $this->answerModel = $answerModel !== null ? $answerModel : new Answer();
    }

    /**
     * @param int $entry_id
     * @param int $question_id
     * @return Answer|null
     */
    public function findByEntryAndQuestion(int $entry_id, int $question_id): ?Answer
    {
        $answers = $this->answerModel->where('entry_id', $entry_id)->where('question_id', $question_id)->get();
        $answer = null;

        if (sizeof($answers) > 0)
        {
            $answer = $answers[0];
        }

        return $answer;
    }
}
