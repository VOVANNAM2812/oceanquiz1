<?php

namespace App\Http\Controllers;

use App\Models\QuestionSet;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function index(Request $request)
    {
        $data = $request->validate(['search' => 'nullable|string|max:100']);
        $sets = QuestionSet::where('status', 'published')->with('article')->withCount('questions')
            ->when($data['search'] ?? null, fn ($q, $search) => $q->where('title', 'like', '%'.$search.'%'))
            ->latest('id')->paginate(12)->withQueryString();
        $completed = $request->user()->attempts()->where('status', 'completed')->count();
        return view('library', compact('sets', 'completed'));
    }

    public function read(QuestionSet $questionSet)
    {
        abort_unless($questionSet->status === 'published', 404);
        $questionSet->load('article');
        return view('read', ['set' => $questionSet]);
    }
}
