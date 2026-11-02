<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, QuestionSet, AiGenerationLog};
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AiGenerationController extends Controller
{
    public function index()
    {
        return view('admin.ai', [
            'articles' => Article::orderBy('title')->get(['id', 'title']),
            'logs' => AiGenerationLog::with(['article', 'user'])->latest('id')->paginate(15),
            'configured' => (bool) config('services.gemini.key'),
        ]);
    }

    public function store(Request $request, GeminiService $gemini)
    {
        $data = $request->validate(['article_id' => 'required|integer|exists:articles,id', 'title' => 'required|string|max:200']);
        $article = Article::findOrFail($data['article_id']);
        $log = AiGenerationLog::create(['user_id' => $request->user()->id, 'article_id' => $article->id, 'model' => config('services.gemini.model'), 'status' => 'pending']);
        try {
            // API có thể chờ 90 giây; PHP cần đủ thời gian để xử lý và ghi log.
            $executionLimit = (int) ini_get('max_execution_time');
            if ($executionLimit > 0 && $executionLimit < 120) {
                set_time_limit(120);
            }
            $text = $gemini->generate($article->content);
            $key = config('services.gemini.key');
            $safeText = $key ? str_replace($key, '[REDACTED]', $text) : $text;
            $log->update(['response_text' => mb_substr($safeText, 0, 30000)]);
            $questions = $gemini->validateQuestions($text);
            $set = DB::transaction(function () use ($article, $data, $questions, $log) {
                $current = Article::whereKey($article->id)->lockForUpdate()->firstOrFail();
                if ($current->content !== $article->content) throw new RuntimeException('Bài viết vừa thay đổi trong lúc sinh câu hỏi. Hãy thử lại với nội dung mới.');
                $set = QuestionSet::create(['article_id' => $current->id, 'title' => $data['title'], 'status' => 'draft', 'source' => 'ai', 'description' => 'Bộ câu hỏi sinh từ bài '.$current->title]);
                foreach ($questions as $index => $question) {
                    $q = $set->questions()->create(['content' => trim($question['content']), 'explanation' => trim($question['explanation']), 'position' => $index + 1]);
                    foreach ($question['options'] as $i => $option) $q->options()->create(['content' => trim($option), 'position' => $i + 1, 'is_correct' => $i === $question['correct_index']]);
                }
                $log->update(['status' => 'success', 'question_set_id' => $set->id]);
                return $set;
            });
        } catch (\Throwable $e) {
            $message = match (true) {
                $e instanceof ValidationException, $e instanceof \JsonException => 'Dữ liệu AI không hợp lệ. Không lưu câu hỏi nào; hãy xem phản hồi trong nhật ký.',
                $e instanceof RuntimeException => $e->getMessage(),
                default => 'Không thể hoàn tất sinh câu hỏi. Không lưu bộ câu hỏi; vui lòng thử lại.',
            };
            $log->update(['status' => 'failed', 'error_message' => $message]);
            return back()->withInput()->withErrors(['gemini' => $message]);
        }
        return to_route('admin.question-sets.edit', $set)->with('success', 'Gemini đã tạo 5 câu nháp. Hãy kiểm tra sự chính xác trước khi xuất bản.');
    }
}
