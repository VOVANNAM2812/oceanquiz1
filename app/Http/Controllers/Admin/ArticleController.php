<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin.articles.index', ['articles' => Article::withCount('questionSets')->latest('id')->paginate(15)]);
    }

    public function create() { return view('admin.articles.form', ['article' => new Article]); }
    public function edit(Article $article) { return view('admin.articles.form', compact('article')); }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:200', 'content' => 'required|string|max:100000',
            'image_url' => 'nullable|url:http,https|max:2048',
            'source_name' => 'nullable|string|max:255', 'source_url' => 'nullable|url:http,https|max:2048',
        ]);
    }

    public function store(Request $request)
    {
        Article::create($this->validated($request));
        return to_route('admin.articles.index')->with('success', 'Đã thêm bài kiến thức.');
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($article, $data) {
            $article = Article::whereKey($article->id)->lockForUpdate()->firstOrFail();
            if ($article->hasAttempts()) {
                throw ValidationException::withMessages(['article' => 'Bài đã có lượt làm liên quan. Hãy tạo bài mới để giữ nguyên lịch sử.']);
            }
            $article->update($data);
        });
        return to_route('admin.articles.index')->with('success', 'Đã cập nhật bài kiến thức.');
    }

    public function destroy(Article $article)
    {
        DB::transaction(function () use ($article) {
            $article = Article::whereKey($article->id)->lockForUpdate()->firstOrFail();
            if ($article->questionSets()->exists()) {
                throw ValidationException::withMessages(['article' => 'Bài đã gắn với bộ câu hỏi nên không thể xóa.']);
            }
            $article->delete();
        });
        return back()->with('success', 'Đã xóa bài viết chưa được sử dụng.');
    }
}
