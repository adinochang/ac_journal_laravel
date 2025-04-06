<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Question extends Model
{
    protected $fillable = [
        'label',
        'required',
        'enabled',
    ];



    /**
     * Returns a list of answers to this question
     *
     * @return HasMany
     */
    public function answer(): HasMany
    {
        return $this->hasMany(Answer::class);
    }



    /**
     * Returns a list of active questions
     *
     * @return Collection
     */
    public function enabledQuestions(): Collection
    {
        return $this->where('enabled', 1)->OrderBy('id')->get();
    }



    /**
     * Returns a list of questions that must be answered
     *
     * @return Collection
     */
    public function requiredQuestions(): Collection
    {
        return $this->where('enabled', 1)->where('required', 1)->OrderBy('id')->get();
    }



    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return 'ac_journal_questions';
    }
}
