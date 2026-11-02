<?php

namespace App\Http\Controllers;

use App\Models\{Article, Attempt, QuestionSet, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    private function authorizeAttempt(Request $request, Attempt $attempt): void
    {
        abort_unless($attempt->user_id === $request->user()->id, 403);
    }

    public function start(Request $request, QuestionSet $questionSet)
    {
        $attempt = DB::transaction(function () use ($request, $questionSet) {
            // Khóa tài khoản để hai tab không tạo hai lượt đang làm.
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            Article::whereKey($questionSet->article_id)->lockForUpdate()->firstOrFail();
            $set = QuestionSet::whereKey($questionSet->id)->lockForUpdate()->firstOrFail();
            $active = $request->user()->attempts()->where('question_set_id', $set->id)->where('status', 'in_progress')->first();
            if ($active) return $active;
            abort_unless($set->status === 'published', 404);
            if (! $set->isReady()) {
                throw ValidationException::withMessages(['quiz' => 'Bộ câu hỏi chưa đủ 5 câu hợp lệ để bắt đầu.']);
            }
            return $request->user()->attempts()->create(['question_set_id' => $set->id, 'started_at' => now(), 'status' => 'in_progress']);
        });
        return to_route('attempts.play', $attempt);
    }

    public function play(Request $request, Attempt $attempt)
    {
        $this->authorizeAttempt($request, $attempt);
        if ($attempt->status === 'completed') return to_route('attempts.result', $attempt);
        $set = $attempt->questionSet;
        // Chỉ lấy dữ liệu cần hiển thị. Không tải đáp án đúng/giải thích.
        $questions = $set->questions()->select('id', 'question_set_id', 'content', 'position')
            ->with(['options:id,question_id,content,position'])->get();
        return view('quiz.play', compact('attempt', 'set', 'questions'));
    }

    public function submit(Request $request, Attempt $attempt)
    {
        $this->authorizeAttempt($request, $attempt);
        DB::transaction(function () use ($request, $attempt) {
            // Kiểm tra lại sau khi khóa để gửi đồng thời vẫn chỉ chấm một lần.
            $attempt = Attempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();
            if ($attempt->status === 'completed') return;
            $data = $request->validate(['answers' => 'sometimes|array', 'answers.*' => 'nullable|integer']);
            $answers = $data['answers'] ?? [];
            $questions = $attempt->questionSet->questions()->with('options')->get()->keyBy('id');
            foreach ($answers as $questionId => $optionId) {
                if (! ctype_digit((string) $questionId) || ! $questions->has((int) $questionId)) {
                    throw ValidationException::withMessages(['answers' => 'Có câu hỏi không thuộc lượt làm này.']);
                }
                if ($optionId !== null && ! $questions[(int) $questionId]->options->contains('id', (int) $optionId)) {
                    throw ValidationException::withMessages(['answers' => 'Có lựa chọn không thuộc đúng câu hỏi.']);
                }
            }
            $correctCount = 0;
            foreach ($questions as $question) {
                $option = $question->options->firstWhere('id', $answers[$question->id] ?? null);
                $correct = $option?->is_correct ?? false;
                $attempt->answers()->create([
                    'question_id' => $question->id, 'option_id' => $option?->id,
                    'is_correct' => $correct, 'score' => $correct ? 10 : 0,
                ]);
                if ($correct) $correctCount++;
            }
            $attempt->update(['status' => 'completed', 'finished_at' => now(), 'correct_count' => $correctCount, 'total_score' => $correctCount * 10]);
        });
        return to_route('attempts.result', $attempt);
    }

    public function result(Request $request, Attempt $attempt)
    {
        $this->authorizeAttempt($request, $attempt);
        if ($attempt->status !== 'completed') return to_route('attempts.play', $attempt);
        $attempt->load(['questionSet', 'answers.question.options', 'answers.option']);
        return view('quiz.result', compact('attempt'));
    }
}
