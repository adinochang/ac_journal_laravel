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
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return 'ac_journal_questions';
    }
}
