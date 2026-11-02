<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $fillable = ['user_id', 'question_set_id', 'status', 'started_at', 'finished_at', 'total_score', 'correct_count'];
    protected $casts = ['started_at' => 'datetime', 'finished_at' => 'datetime'];
    protected $dateFormat = 'Y-m-d H:i:s.u';
    public function user() { return $this->belongsTo(User::class); }
    public function questionSet() { return $this->belongsTo(QuestionSet::class); }
    public function answers() { return $this->hasMany(AttemptAnswer::class); }
}
