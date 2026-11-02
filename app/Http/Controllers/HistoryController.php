<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $attempts = $request->user()->attempts()->where('status', 'completed')
            ->with('questionSet')->orderByDesc('finished_at')->orderByDesc('id')->paginate(10);
        return view('history.index', compact('attempts'));
    }
}
