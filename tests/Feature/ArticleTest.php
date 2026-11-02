<?php

namespace Tests\Feature;

use App\Models\{User, Article, QuestionSet, Attempt};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    public function test_article_crud_and_url_validation(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/articles', ['title' => 'Cá voi', 'content' => 'Kiến thức biển', 'source_url' => 'javascript:alert(1)'])->assertSessionHasErrors('source_url');
        $this->post('/admin/articles', ['title' => 'Cá voi', 'content' => 'Kiến thức biển'])->assertSessionHasNoErrors();
        $article = Article::firstOrFail();
        $this->patch('/admin/articles/'.$article->id, ['title' => 'Cá voi xanh', 'content' => 'Nội dung mới'])->assertSessionHasNoErrors();
        $this->assertSame('Cá voi xanh', $article->fresh()->title);
        $this->delete('/admin/articles/'.$article->id)->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_article_with_attempt_cannot_change_or_be_deleted(): void
    {
        $admin = $this->admin();
        $article = Article::create(['title' => 'Bài gốc', 'content' => 'Nội dung gốc']);
        $set = QuestionSet::create(['article_id' => $article->id, 'title' => 'Quiz']);
        Attempt::create(['user_id' => $admin->id, 'question_set_id' => $set->id, 'started_at' => now()]);
        $this->actingAs($admin)->patch('/admin/articles/'.$article->id, ['title' => 'Đã sửa', 'content' => 'Khác'])->assertSessionHasErrors();
        $this->delete('/admin/articles/'.$article->id)->assertSessionHasErrors();
        $this->assertSame('Bài gốc', $article->fresh()->title);
    }
}
