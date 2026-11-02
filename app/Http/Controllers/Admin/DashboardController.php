<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, QuestionSet, Attempt, AiGenerationLog};

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'articleCount' => Article::count(),
            'setCount' => QuestionSet::where('status', 'published')->count(),
            'attemptCount' => Attempt::where('status', 'completed')->count(),
            'logs' => AiGenerationLog::with('article')->latest('id')->limit(5)->get(),
        ]);
    }
}
