<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;


class Entry extends Model
{
    /**
     * Returns the answers in this journal entry
     *
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }



    /**
     * Returns an except from the first answer in this entry to display in the dashboard
     *
     * @param int $required_length
     * @return string
     */
    public function answerExcerpt(int $required_length): string
    {
        $excerpt = '';

        // get the first answer from this entry
        $firstAnswer = $this->answers()->first();

        if (isset($firstAnswer))
        {
            $excerpt = $firstAnswer->answer_text;

            // if the answer is too long, truncate it
            if (strlen($excerpt) > $required_length)
            {
                $excerpt = substr($excerpt, 0, $required_length) . '...';
            }
        }

        return $excerpt;
    }



    /**
     * Performs validation and returns the array of answers if validation is successful
     * TODO: This should be in the controller
     *
     * @param Request $request
     * @param Collection $requiredQuestions
     * @return array
     */
    public function performRequestValidation(Request $request, Collection $requiredQuestions): array
    {
        $validationRules = [];

        foreach($requiredQuestions as $requiredQuestion)
        {
            $validationRules['answer_' . $requiredQuestion->id] = 'required';
        }

        return $request->validate($validationRules);
    }



    /**
     * Creates a new journal entry and saves the answers
     *
     * @param  Answer $answerModel
     * @param  array $answers_array
     * @return bool
     */
    public function saveAnswers(Answer $answerModel, array $answers_array): bool
    {
        if (!isset($answers_array) || sizeof($answers_array) == 0)
        {
            return false;
        }

        // create an entry record
        $this->save();

        // save answers for the entry
        foreach($answers_array as $questionId => $answerText)
        {
            if (strlen($answerText) > 0)
            {
                $answerModel->create([
                    'entry_id' => $this->id,
                    'question_id' => $questionId,
                    'answer_text' => $answerText,
                ]);
            }
        }

        return true;
    }



    /**
     * Updates the timestamp of the entry and updates the answers
     *
     * @param Answer $answerModel
     * @param array $answers
     * @return bool
     */
    public function updateAnswers(Answer $answerModel, array $answers): bool
    {
        if (!isset($answers) || sizeof($answers) == 0)
        {
            return false;
        }

        // update entry record
        $this->setUpdatedAt(now());
        $this->save();

        foreach($answers as $questionId => $answerText)
        {
            $answer = $answerModel->findByEntryAndQuestion($this->id, $questionId);

            $answer->answer_text = $answerText ?? '';

            $answer->save();
        }

        return true;
    }



    /**
     * Overrides default delete method. Must delete the entry answers before deleting the entry.
     *
     * @return void
     * @throws Exception
     */
    public function delete(): void
    {
        // delete answers first
        foreach($this->answers as $answer)
        {
            $answer->delete();
        }

        // delete entry
        parent::delete();
    }



    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return 'ac_journal_entries';
    }
}
