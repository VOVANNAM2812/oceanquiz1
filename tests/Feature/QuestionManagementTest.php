<?php

namespace Tests\Feature;

use App\Models\{User, Article, QuestionSet, Attempt};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\{TestCase, CreatesQuiz};

class QuestionManagementTest extends TestCase
{
    use RefreshDatabase, CreatesQuiz;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(); $user->role = 'admin'; $user->save(); $this->actingAs($user);
    }

    public function test_create_five_questions_then_publish_and_block_sixth(): void
    {
        $article = Article::create(['title' => 'Biển', 'content' => 'Nội dung']);
        $this->post('/admin/question-sets', ['article_id' => $article->id, 'title' => 'Bộ mới', 'status' => 'published'])->assertSessionHasNoErrors();
        $set = QuestionSet::firstOrFail();
        $this->assertSame('draft', $set->status);
        $this->post(route('admin.question-sets.publish', $set))->assertSessionHasErrors();
        $data = ['content' => 'Câu hỏi?', 'options' => ['A', 'B', 'C', 'D'], 'correct_index' => 0];
        for ($i=0; $i<5; $i++) $this->post(route('admin.question-sets.questions.store', $set), $data)->assertSessionHasNoErrors();
        $this->post(route('admin.question-sets.questions.store', $set), $data)->assertSessionHasErrors();
        $this->get(route('admin.question-sets.edit', $set))->assertOk();
        $this->get(route('admin.question-sets.questions.edit', [$set, $set->questions->first()]))->assertOk();
        $this->post(route('admin.question-sets.publish', $set))->assertSessionHasNoErrors();
        $this->assertSame('published', $set->fresh()->status);
    }

    public function test_question_validation_nested_ownership_and_used_set_protection(): void
    {
        $set = $this->createQuiz('draft'); $other = $this->createQuiz('draft');
        $q = $set->questions->first();
        $this->patch(route('admin.question-sets.questions.update', [$set, $q]), ['content' => 'Bad', 'options' => ['A', 'B'], 'correct_index' => 4])->assertSessionHasErrors();
        $this->delete(route('admin.question-sets.questions.destroy', [$other, $q]))->assertNotFound();
        Attempt::create(['user_id' => auth()->id(), 'question_set_id' => $set->id, 'started_at' => now()]);
        $this->patch(route('admin.question-sets.update', $set), ['title' => 'Changed'])->assertSessionHasErrors();
        $this->delete(route('admin.question-sets.questions.destroy', [$set, $q]))->assertSessionHasErrors();
        $this->delete(route('admin.question-sets.destroy', $set))->assertSessionHasErrors();
        $this->post(route('admin.question-sets.publish', $set))->assertSessionHasNoErrors();
        $this->post(route('admin.question-sets.unpublish', $set))->assertSessionHasNoErrors();
        $this->assertSame(5, $set->questions()->count());
    }

    public function test_unused_draft_can_be_deleted_without_deleting_article(): void
    {
        $set = $this->createQuiz('draft');
        $this->delete(route('admin.question-sets.destroy', $set))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('question_sets', ['id' => $set->id]);
        $this->assertDatabaseHas('articles', ['id' => $set->article_id]);
    }
}
