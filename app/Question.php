<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * Returns a list of answers to this question
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answer()
    {
        return $this->hasMany(Answer::class);
    }



    /**
     * Returns a list of active questions
     *
     * @return Array
     */
    public function enabled_questions()
    {
        return $this->where('enabled', 1)->OrderBy('id')->get();
    }



    /**
     * Returns a list of questions that must be answered
     *
     * @return Array
     */
    public function required_questions()
    {
        return $this->where('enabled', 1)->where('required', 1)->OrderBy('id')->get();
    }
}
