<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
