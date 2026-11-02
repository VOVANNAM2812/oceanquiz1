<?php

namespace App\Http\Controllers;

use App\Models\{Attempt, QuestionSet};
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate(['question_set_id' => 'nullable|integer|min:1']);
        $sets = QuestionSet::where('status', 'published')->orderBy('title')->get(['id', 'title']);
        $selected = isset($data['question_set_id']) ? $sets->firstWhere('id', (int) $data['question_set_id']) : $sets->first();
        if (isset($data['question_set_id'])) abort_unless($selected, 404);
        $rankings = null;
        if ($selected) {
            // Loại một lượt nếu cùng user/bộ có lượt tốt hơn. Điểm và thời gian
            // luôn thuộc cùng một dòng, tránh ghép MAX điểm với MIN giờ của lượt khác.
            $rankings = Attempt::where('question_set_id', $selected->id)->where('status', 'completed')->whereNotNull('finished_at')
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')->from('attempts as better')
                        ->whereColumn('better.user_id', 'attempts.user_id')
                        ->whereColumn('better.question_set_id', 'attempts.question_set_id')
                        ->where('better.status', 'completed')->whereNotNull('better.finished_at')
                        ->where(function ($q) {
                            $q->whereColumn('better.total_score', '>', 'attempts.total_score')
                                ->orWhere(function ($sameScore) {
                                    $sameScore->whereColumn('better.total_score', 'attempts.total_score')
                                        ->where(function ($time) {
                                            $time->whereColumn('better.finished_at', '<', 'attempts.finished_at')
                                                ->orWhere(function ($sameTime) {
                                                    $sameTime->whereColumn('better.finished_at', 'attempts.finished_at')
                                                        ->whereColumn('better.id', '<', 'attempts.id');
                                                });
                                        });
                                });
                        });
                })->with('user:id,name')->orderByDesc('total_score')->orderBy('finished_at')->orderBy('user_id')
                ->paginate(10)->withQueryString();
        }
        return view('leaderboard.index', compact('sets', 'selected', 'rankings'));
    }
}
