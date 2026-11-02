<?php

namespace Tests\Feature;

use App\Models\{User, QuestionSet};
use Database\Seeders\OceanContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_is_repeatable_and_library_hides_drafts(): void
    {
        $this->seed(OceanContentSeeder::class);
        $this->seed(OceanContentSeeder::class);
        $this->assertSame(4, QuestionSet::count());
        $this->assertTrue(QuestionSet::where('status', 'published')->get()->every(fn($s) => $s->isReady()));
        $this->actingAs(User::factory()->create())->get('/home')->assertOk()->assertSee('Cá voi xanh')->assertDontSee('Bộ nháp đang biên soạn');
        $this->get('/home?search=Rùa')->assertOk()->assertSee('Rùa biển:')->assertDontSee('Cá voi xanh:');
        $this->get(route('question-sets.read', QuestionSet::where('status', 'published')->first()))->assertOk()->assertSee('NOAA');
    }
}
