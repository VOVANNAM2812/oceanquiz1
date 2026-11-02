<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    protected $fillable = ['attempt_id', 'question_id', 'option_id', 'is_correct', 'score'];
    protected $casts = ['is_correct' => 'boolean'];
    public function attempt() { return $this->belongsTo(Attempt::class); }
    public function question() { return $this->belongsTo(Question::class); }
    public function option() { return $this->belongsTo(QuestionOption::class); }
}
