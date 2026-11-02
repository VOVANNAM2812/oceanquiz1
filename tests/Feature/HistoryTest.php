<?php

namespace Tests\Feature;

use App\Models\{User, Attempt};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\{TestCase, CreatesQuiz};

class HistoryTest extends TestCase
{
    use RefreshDatabase, CreatesQuiz;

    public function test_history_shows_only_own_completed_attempts_with_pagination(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $set = $this->createQuiz();
        foreach (range(1, 12) as $i) {
            Attempt::create(['user_id' => $user->id, 'question_set_id' => $set->id, 'started_at' => now(), 'finished_at' => now()->addSeconds($i), 'status' => 'completed']);
        }
        Attempt::create(['user_id' => $user->id, 'question_set_id' => $set->id, 'started_at' => now()]);
        $foreign = Attempt::create(['user_id' => $other->id, 'question_set_id' => $set->id, 'started_at' => now(), 'finished_at' => now(), 'status' => 'completed']);
        $response = $this->actingAs($user)->get('/history')->assertOk();
        $this->assertSame(12, $response->viewData('attempts')->total());
        $this->assertSame(10, $response->viewData('attempts')->count());
        $this->assertFalse($response->viewData('attempts')->contains('id', $foreign->id));
        $this->get('/history?page=2')->assertOk()->assertViewHas('attempts', fn ($rows) => $rows->count() === 2);
    }
}
