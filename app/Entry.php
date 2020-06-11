<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
