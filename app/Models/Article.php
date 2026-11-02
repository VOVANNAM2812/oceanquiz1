<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'content', 'image_url', 'source_name', 'source_url', 'wiki_page_id', 'license'];

    public function questionSets() { return $this->hasMany(QuestionSet::class); }

    public function hasAttempts(): bool
    {
        return $this->questionSets()->whereHas('attempts')->exists();
    }
}
