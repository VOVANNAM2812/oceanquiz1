<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    protected $fillable = ['article_id', 'title', 'description', 'status', 'source'];

    public function article() { return $this->belongsTo(Article::class); }
    public function questions() { return $this->hasMany(Question::class)->orderBy('position'); }
    public function attempts() { return $this->hasMany(Attempt::class); }

    public function isReady(): bool
    {
        $questions = $this->questions()->with('options')->get();
        return $questions->count() === 5 && $questions->every(fn ($q) =>
            trim($q->content) !== '' && $q->options->count() === 4
            && $q->options->every(fn ($o) => trim($o->content) !== '')
            && $q->options->where('is_correct', true)->count() === 1
        );
    }
}
