<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;


class Answer extends Model
{
    protected $fillable = [
        'entry_id',
        'question_id',
        'answer_text',
    ];



    /**
     * Returns the entry that contains this answer
     *
     * @return BelongsTo
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }



    /**
     * Returns the question that this answer relates to
     *
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }



    /**
     * Find all the answers from $request and format them into an array with the format of:
     * [ <question_id> => <answer_text> ]
     *
     * This array can then be fed into the save function in the Entry model
     *
     * @param Request $request
     * @return array
     */
    public function getAnswersArrayFromRequest(Request $request): array
    {
        $answers = [];

        foreach ($request->all() as $fieldName => $value)
        {
            if (strpos($fieldName, 'answer_') !== false)
            {
                // format the answers in the format of [ question_id => answer text ]
                $answers[str_replace('answer_', '', $fieldName)] = $value;
            }
        }

        return $answers;
    }



    /**
     * Finds an answer filtered by entry_id and question_id
     *
     * @param int $entry_id
     * @param int $question_id
     *
     * @return ?Answer
     */
    public function findByEntryAndQuestion(int $entry_id, int $question_id): ?Answer
    {
        $answers = $this->where('entry_id', $entry_id)->where('question_id', $question_id)->get();
        $answer = null;

        if (sizeof($answers) > 0)
        {
            $answer = $answers[0];
        }

        return $answer;
    }



    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return 'ac_journal_answers';
    }
}
