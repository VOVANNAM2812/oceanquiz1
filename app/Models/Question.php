<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question_set_id', 'content', 'explanation', 'position'];
    public function questionSet() { return $this->belongsTo(QuestionSet::class); }
    public function options() { return $this->hasMany(QuestionOption::class)->orderBy('position'); }
}
