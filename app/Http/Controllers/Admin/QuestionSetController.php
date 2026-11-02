<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, QuestionSet};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionSetController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['status' => 'nullable|in:draft,published']);
        $sets = QuestionSet::with('article')->withCount(['questions', 'attempts'])
            ->when($data['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->latest('id')->paginate(15)->withQueryString();
        return view('admin.question-sets.index', compact('sets'));
    }

    public function create()
    {
        return view('admin.question-sets.form', ['set' => new QuestionSet, 'articles' => Article::orderBy('title')->get(['id', 'title'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['article_id' => 'required|integer|exists:articles,id', 'title' => 'required|string|max:200', 'description' => 'nullable|string|max:2000']);
        $set = DB::transaction(function () use ($data) {
            Article::whereKey($data['article_id'])->lockForUpdate()->firstOrFail();
            return QuestionSet::create($data + ['status' => 'draft']);
        });
        return to_route('admin.question-sets.edit', $set)->with('success', 'Đã tạo bộ nháp. Hãy thêm đủ 5 câu hỏi.');
    }

    public function edit(QuestionSet $questionSet)
    {
        $questionSet->load(['article', 'questions.options']);
        return view('admin.question-sets.form', ['set' => $questionSet, 'articles' => collect()]);
    }

    private function lockSet(QuestionSet $set): QuestionSet
    {
        Article::whereKey($set->article_id)->lockForUpdate()->firstOrFail();
        return QuestionSet::whereKey($set->id)->lockForUpdate()->firstOrFail();
    }

    private function ensureEditable(QuestionSet $set): void
    {
        if ($set->status !== 'draft' || $set->attempts()->exists()) {
            throw ValidationException::withMessages(['set' => 'Chỉ sửa/xóa bộ nháp chưa có lượt làm. Bộ đã sử dụng cần được giữ nguyên.']);
        }
    }

    public function update(Request $request, QuestionSet $questionSet)
    {
        $data = $request->validate(['title' => 'required|string|max:200', 'description' => 'nullable|string|max:2000']);
        DB::transaction(function () use ($questionSet, $data) {
            $set = $this->lockSet($questionSet);
            $this->ensureEditable($set);
            $set->update($data);
        });
        return back()->with('success', 'Đã lưu thông tin bộ câu hỏi.');
    }

    public function destroy(QuestionSet $questionSet)
    {
        DB::transaction(function () use ($questionSet) {
            $set = $this->lockSet($questionSet);
            $this->ensureEditable($set);
            foreach ($set->questions as $q) { $q->options()->delete(); $q->delete(); }
            $set->delete();
        });
        return to_route('admin.question-sets.index')->with('success', 'Đã xóa bộ nháp chưa sử dụng.');
    }

    public function publish(QuestionSet $questionSet)
    {
        DB::transaction(function () use ($questionSet) {
            $set = $this->lockSet($questionSet);
            if (! $set->isReady()) {
                throw ValidationException::withMessages(['set' => 'Cần đúng 5 câu hợp lệ, mỗi câu 4 lựa chọn và 1 đáp án đúng để xuất bản.']);
            }
            $set->update(['status' => 'published']);
        });
        return back()->with('success', 'Đã xuất bản bộ câu hỏi.');
    }

    public function unpublish(QuestionSet $questionSet)
    {
        DB::transaction(fn () => $this->lockSet($questionSet)->update(['status' => 'draft']));
        return back()->with('success', 'Đã gỡ xuất bản. Lịch sử và lượt đang làm được giữ nguyên.');
    }
}
