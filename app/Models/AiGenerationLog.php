<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGenerationLog extends Model
{
    protected $fillable = ['user_id', 'article_id', 'question_set_id', 'status', 'model', 'error_message', 'response_text'];
    public function article() { return $this->belongsTo(Article::class); }
    public function user() { return $this->belongsTo(User::class); }
}
