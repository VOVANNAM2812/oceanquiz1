<?php

namespace Tests\Feature;

use App\Models\{User, Attempt};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\{TestCase, CreatesQuiz};

class QuizTest extends TestCase
{
    use RefreshDatabase, CreatesQuiz;

    private function startQuiz(): Attempt
    {
        $this->actingAs(User::factory()->create());
        $set = $this->createQuiz();
        $this->post(route('attempts.start', $set))->assertRedirect();
        return Attempt::firstOrFail();
    }

    public function test_start_resumes_and_quiz_does_not_expose_correctness(): void
    {
        $attempt = $this->startQuiz();
        $this->post(route('attempts.start', $attempt->questionSet))->assertRedirect(route('attempts.play', $attempt));
        $this->assertSame(1, Attempt::count());
        $this->get(route('attempts.play', $attempt))->assertOk()->assertDontSee('GIẢI_THÍCH_BÍ_MẬT')->assertDontSee('is_correct');
    }

    public function test_server_grades_and_repeated_submission_does_not_change_result(): void
    {
        $attempt = $this->startQuiz();
        $answers = $attempt->questionSet->questions->mapWithKeys(fn ($q) => [$q->id => $q->options->first()->id])->all();
        $this->post(route('attempts.submit', $attempt), ['answers' => $answers, 'total_score' => 9000])->assertRedirect(route('attempts.result', $attempt));
        $attempt->refresh();
        $this->assertSame(50, $attempt->total_score);
        $this->assertSame(5, $attempt->correct_count);
        $finished = $attempt->finished_at->format('Y-m-d H:i:s.u');
        $this->travel(10)->minutes();
        $this->post(route('attempts.submit', $attempt), ['answers' => []])->assertRedirect();
        $this->assertSame(50, $attempt->fresh()->total_score);
        $this->assertSame($finished, $attempt->fresh()->finished_at->format('Y-m-d H:i:s.u'));
        $this->assertSame(5, $attempt->answers()->count());
        $this->get(route('attempts.result', $attempt))->assertOk()->assertSee('GIẢI_THÍCH_BÍ_MẬT_1');
    }

    public function test_blank_answers_score_zero_and_new_attempt_does_not_overwrite_old(): void
    {
        $attempt = $this->startQuiz();
        $this->post(route('attempts.submit', $attempt), [])->assertSessionHasNoErrors();
        $this->assertSame(0, $attempt->fresh()->total_score);
        $this->assertSame(5, $attempt->answers()->whereNull('option_id')->count());
        $this->post(route('attempts.start', $attempt->questionSet));
        $this->assertSame(2, Attempt::count());
    }

    public function test_foreign_question_and_option_are_rejected_atomically(): void
    {
        $attempt = $this->startQuiz();
        $questions = $attempt->questionSet->questions;
        $this->post(route('attempts.submit', $attempt), ['answers' => [$questions[0]->id => $questions[1]->options->first()->id]])->assertSessionHasErrors('answers');
        $other = $this->createQuiz();
        $this->post(route('attempts.submit', $attempt), ['answers' => [$other->questions->first()->id => $other->questions->first()->options->first()->id]])->assertSessionHasErrors('answers');
        $this->assertSame('in_progress', $attempt->fresh()->status);
        $this->assertSame(0, $attempt->answers()->count());
    }

    public function test_draft_and_other_users_attempt_are_protected(): void
    {
        $attempt = $this->startQuiz();
        $draft = $this->createQuiz('draft');
        $this->get(route('question-sets.read', $draft))->assertNotFound();
        $this->post(route('attempts.start', $draft))->assertNotFound();
        $this->actingAs(User::factory()->create());
        $this->get(route('attempts.play', $attempt))->assertForbidden();
        $this->post(route('attempts.submit', $attempt))->assertForbidden();
        $this->get(route('attempts.result', $attempt))->assertForbidden();
    }

    public function test_unpublished_set_allows_existing_attempt_but_not_new_one(): void
    {
        $attempt = $this->startQuiz();
        $attempt->questionSet->update(['status' => 'draft']);
        $this->get(route('attempts.play', $attempt))->assertOk();
        $this->post(route('attempts.submit', $attempt))->assertSessionHasNoErrors();
        $this->get(route('attempts.result', $attempt))->assertOk();
        $this->post(route('attempts.start', $attempt->questionSet))->assertNotFound();
    }
}
