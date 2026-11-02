<?php

namespace Tests;

use App\Models\{Article, QuestionSet};

trait CreatesQuiz
{
    protected function createQuiz(string $status = 'published'): QuestionSet
    {
        $article = Article::create(['title' => 'Cá voi', 'content' => 'Cá voi thở bằng phổi.']);
        $set = QuestionSet::create(['article_id' => $article->id, 'title' => 'Khám phá cá voi', 'status' => $status]);
        for ($i = 1; $i <= 5; $i++) {
            $q = $set->questions()->create(['content' => 'Câu hỏi '.$i, 'explanation' => 'GIẢI_THÍCH_BÍ_MẬT_'.$i, 'position' => $i]);
            for ($j = 1; $j <= 4; $j++) $q->options()->create(['content' => 'Lựa chọn '.$j, 'is_correct' => $j === 1, 'position' => $j]);
        }
        return $set;
    }
}
