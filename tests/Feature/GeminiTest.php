<?php

namespace Tests\Feature;

use App\Models\{User, Article, QuestionSet, AiGenerationLog};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['services.gemini.key' => 'fake-test-key', 'services.gemini.model' => 'test-model']);
        $user = User::factory()->create(); $user->role = 'admin'; $user->save(); $this->actingAs($user);
    }

    private function data(): array
    {
        return ['questions' => array_map(fn ($i) => ['content' => 'Câu '.$i, 'options' => ['Một', 'Hai', 'Ba', 'Bốn'], 'correct_index' => $i % 4, 'explanation' => 'Giải thích hợp lệ.'], range(1, 5))];
    }

    private function fakeGemini(array $data): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['finishReason' => 'STOP', 'content' => ['parts' => [['text' => json_encode($data, JSON_UNESCAPED_UNICODE)]]]]]])]);
    }

    public function test_valid_generation_creates_only_draft_and_success_log(): void
    {
        $article = Article::create(['title' => 'Biển', 'content' => str_repeat('Dữ liệu. ', 3000)]);
        $this->fakeGemini($this->data());
        $this->get('/admin/ai')->assertOk();
        $this->post('/admin/ai', ['article_id' => $article->id, 'title' => 'Quiz AI'])->assertSessionHasNoErrors();
        $set = QuestionSet::firstOrFail();
        $this->assertSame('draft', $set->status); $this->assertSame('ai', $set->source); $this->assertTrue($set->isReady());
        $this->assertDatabaseHas('ai_generation_logs', ['status' => 'success', 'question_set_id' => $set->id]);
        Http::assertSent(fn ($r) => $r->hasHeader('x-goog-api-key', 'fake-test-key') && !str_contains($r->url(), 'fake-test-key') && mb_strlen($r['contents'][0]['parts'][0]['text']) < 13000);
    }

    public function test_one_invalid_question_rejects_entire_generation_and_keeps_error_log(): void
    {
        $article = Article::create(['title' => 'Biển', 'content' => 'Nội dung']);
        $data = $this->data(); $data['questions'][3]['correct_index'] = 8;
        $this->fakeGemini($data);
        $this->post('/admin/ai', ['article_id' => $article->id, 'title' => 'Quiz AI'])->assertSessionHasErrors('gemini');
        $this->assertSame(0, QuestionSet::count());
        $this->assertSame(0, \App\Models\Question::count());
        $this->assertSame('failed', AiGenerationLog::first()->status);
        $this->assertNotEmpty(AiGenerationLog::first()->response_text);
    }

    public function test_missing_key_and_api_error_do_not_create_sets_or_expose_key(): void
    {
        $article = Article::create(['title' => 'Biển', 'content' => 'Nội dung']);
        config(['services.gemini.key' => null]);
        $this->post('/admin/ai', ['article_id' => $article->id, 'title' => 'Quiz AI'])->assertSessionHasErrors();
        Http::assertNothingSent();
        config(['services.gemini.key' => 'fake-test-key']);
        Http::fake(['*' => Http::response(['error' => 'fake-test-key'], 429)]);
        $this->post('/admin/ai', ['article_id' => $article->id, 'title' => 'Quiz AI'])->assertSessionHasErrors();
        $this->assertSame(0, QuestionSet::count());
        $this->assertSame(2, AiGenerationLog::where('status', 'failed')->count());
        $this->assertFalse(AiGenerationLog::where('error_message', 'like', '%fake-test-key%')->exists());
    }
}
