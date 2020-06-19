<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    /**
     * Returns the answers in this journal entry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }



    /**
     * Returns an except from the first answer in this entry to display in the dashboard
     *
     * @param int $required_length
     * @return string
     */
    public function answer_excerpt($required_length)
    {
        $excerpt = '';

        // get the first answer from this entry
        $first_answer = $this->answers()->first();

        if (isset($first_answer))
        {
            $excerpt = $first_answer->answer_text;

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
     * @return array
     */
    public function perform_request_validation()
    {
        $question_model = new Question();
        $required_questions = $question_model->required_questions();

        $validation_array = [];

        foreach($required_questions as $required_question)
        {
            $validation_array['answer_' . $required_question->id] = 'required';
        }

        return request()->validate($validation_array);
    }



    /**
     * Creates a new journal entry and saves the answers
     *
     * @param  array  $answers_array
     * @return boolean
     */
    public function save_answers($answers_array)
    {
        if (!isset($answers_array) || sizeof($answers_array) == 0)
        {
            return false;
        }



        // create an entry record
        $this->save();



        // save answers for the entry
        foreach($answers_array as $question_id => $answer_text)
        {
            if (strlen($answer_text) > 0)
            {
                Answer::create([
                    'entry_id' => $this->id,
                    'question_id' => $question_id,
                    'answer_text' => $answer_text,
                ]);
            }
        }

        return true;
    }



    /**
     * Updates the timestamp of the entry and updates the answers
     *
     * @param  array  $answers_array
     * @return boolean
     */
    public function update_answers($answers_array)
    {
        if (!isset($answers_array) || sizeof($answers_array) == 0)
        {
            return false;
        }

        // update entry record
        $this->setUpdatedAt(now());
        $this->save();

        // save answers for the entry
        $answer_model = new Answer();

        foreach($answers_array as $question_id => $answer_text)
        {
            $answer = $answer_model->find_by_entry_and_question($this->id, $question_id);

            $answer->answer_text = isset($answer_text) ? $answer_text : '';

            $answer->save();
        }

        return true;
    }



    /**
     * Overrides default delete method. Must delete the entry answers before deleting the entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $answers_array
     * @return boolean
     */
    public function delete()
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
    public function getTable()
    {
        return 'ac_journal_entries';
    }
}
