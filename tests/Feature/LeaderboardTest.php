<?php

namespace Tests\Feature;

use App\Models\{Attempt, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\{TestCase, CreatesQuiz};

class LeaderboardTest extends TestCase
{
    use RefreshDatabase, CreatesQuiz;

    private function record(User $user, $set, int $score, string $time): Attempt
    {
        return Attempt::create(['user_id' => $user->id, 'question_set_id' => $set->id, 'status' => 'completed', 'started_at' => '2026-11-02 00:00:00', 'finished_at' => $time, 'total_score' => $score, 'correct_count' => (int)($score / 10)]);
    }

    public function test_best_score_and_earliest_achievement_are_from_same_attempt(): void
    {
        $set = $this->createQuiz();
        $an = User::factory()->create(['name' => 'An']); $binh = User::factory()->create(['name' => 'Bình']); $chi = User::factory()->create(['name' => 'Chi']);
        $this->record($an, $set, 30, '2026-11-02 01:00:00');
        $best = $this->record($an, $set, 50, '2026-11-02 02:00:00');
        $this->record($an, $set, 50, '2026-11-02 03:00:00');
        $this->record($binh, $set, 50, '2026-11-02 02:10:00');
        $this->record($chi, $set, 40, '2026-11-02 00:00:00');
        Attempt::create(['user_id' => $chi->id, 'question_set_id' => $set->id, 'started_at' => now()]);
        $otherSet = $this->createQuiz(); $this->record($chi, $otherSet, 50, '2026-11-01 00:00:00');
        $response = $this->actingAs($an)->get('/leaderboard?question_set_id='.$set->id)->assertOk();
        $rows = $response->viewData('rankings');
        $this->assertSame([$an->id, $binh->id, $chi->id], $rows->pluck('user_id')->all());
        $this->assertSame($best->id, $rows->first()->id);
        $this->assertSame('2026-11-02 02:00:00', $rows->first()->finished_at->format('Y-m-d H:i:s'));
        $response->assertDontSee($an->email);
    }

    public function test_ties_and_pagination_have_stable_order(): void
    {
        $set = $this->createQuiz(); $users = User::factory()->count(12)->create();
        foreach ($users as $user) $this->record($user, $set, 50, '2026-11-02 02:00:00');
        $this->record($users[0], $set, 50, '2026-11-02 02:00:00');
        $response = $this->actingAs($users[0])->get('/leaderboard?question_set_id='.$set->id.'&page=2')->assertOk();
        $rows = $response->viewData('rankings');
        $this->assertSame(12, $rows->total()); $this->assertSame(11, $rows->firstItem());
        $this->assertSame([$users[10]->id, $users[11]->id], $rows->pluck('user_id')->all());
    }

    public function test_empty_and_draft_leaderboards(): void
    {
        $this->actingAs(User::factory()->create())->get('/leaderboard')->assertOk();
        $set = $this->createQuiz('draft');
        $this->get('/leaderboard?question_set_id='.$set->id)->assertNotFound();
    }
}
