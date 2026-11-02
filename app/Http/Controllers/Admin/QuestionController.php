<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, QuestionSet, Question};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function create(QuestionSet $questionSet)
    {
        return view('admin.questions.form', ['set' => $questionSet, 'question' => new Question]);
    }

    public function edit(QuestionSet $questionSet, Question $question)
    {
        abort_unless($question->question_set_id === $questionSet->id, 404);
        $question->load('options');
        return view('admin.questions.form', ['set' => $questionSet, 'question' => $question]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'content' => 'required|string|max:2000', 'explanation' => 'nullable|string|max:5000',
            'options' => 'required|array|list|size:4', 'options.*' => 'required|string|max:1000',
            'correct_index' => 'required|integer|min:0|max:3',
        ]);
    }

    private function mutate(QuestionSet $questionSet, callable $callback): void
    {
        DB::transaction(function () use ($questionSet, $callback) {
            Article::whereKey($questionSet->article_id)->lockForUpdate()->firstOrFail();
            $set = QuestionSet::whereKey($questionSet->id)->lockForUpdate()->firstOrFail();
            if ($set->status !== 'draft' || $set->attempts()->exists()) {
                throw ValidationException::withMessages(['set' => 'Chỉ biên tập câu hỏi của bộ nháp chưa có lượt làm.']);
            }
            $callback($set);
        });
    }

    private function saveOptions(Question $question, array $data): void
    {
        foreach ($data['options'] as $index => $content) {
            $question->options()->create(['content' => $content, 'position' => $index + 1, 'is_correct' => $index === (int) $data['correct_index']]);
        }
    }

    public function store(Request $request, QuestionSet $questionSet)
    {
        $data = $this->validated($request);
        $this->mutate($questionSet, function ($set) use ($data) {
            $positions = array_values(array_diff(range(1, 5), $set->questions()->pluck('position')->all()));
            if (! $positions) throw ValidationException::withMessages(['set' => 'Bộ đã đủ 5 câu. Không thể thêm câu thứ 6.']);
            $q = $set->questions()->create(['content' => $data['content'], 'explanation' => $data['explanation'] ?? null, 'position' => $positions[0]]);
            $this->saveOptions($q, $data);
        });
        return to_route('admin.question-sets.edit', $questionSet)->with('success', 'Đã thêm câu hỏi.');
    }

    public function update(Request $request, QuestionSet $questionSet, Question $question)
    {
        abort_unless($question->question_set_id === $questionSet->id, 404);
        $data = $this->validated($request);
        $this->mutate($questionSet, function ($set) use ($question, $data) {
            $q = $set->questions()->findOrFail($question->id);
            $q->update(['content' => $data['content'], 'explanation' => $data['explanation'] ?? null]);
            $q->options()->delete();
            $this->saveOptions($q, $data);
        });
        return to_route('admin.question-sets.edit', $questionSet)->with('success', 'Đã cập nhật câu hỏi.');
    }

    public function destroy(QuestionSet $questionSet, Question $question)
    {
        abort_unless($question->question_set_id === $questionSet->id, 404);
        $this->mutate($questionSet, function ($set) use ($question) {
            $q = $set->questions()->findOrFail($question->id);
            $q->options()->delete(); $q->delete();
        });
        return to_route('admin.question-sets.edit', $questionSet)->with('success', 'Đã xóa câu hỏi.');
    }
}
