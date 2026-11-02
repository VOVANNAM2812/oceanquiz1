<?php

namespace Tests\Feature;

use App\Models\{User, Article};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WikipediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        $user = User::factory()->create();
        $user->role = 'admin'; $user->save(); $this->actingAs($user);
    }

    public function test_search_and_import_are_server_side_and_deduplicated(): void
    {
        Http::fake(['vi.wikipedia.org/*' => Http::response(['query' => ['search' => [['pageid' => 42, 'title' => 'Cá voi', 'snippet' => '<b>Kiến thức</b>']], 'pages' => [['pageid' => 42, 'title' => 'Cá voi', 'ns' => 0, 'extract' => 'Cá voi thở bằng phổi.']]]])]);
        $this->get('/admin/wikipedia?search=ca')->assertOk()->assertSee('Cá voi');
        $this->get('/admin/wikipedia?page_id=42')->assertOk()->assertSee('Cá voi thở bằng phổi.');
        $this->post('/admin/wikipedia', ['page_id' => 42, 'title' => 'Giả mạo'])->assertSessionHasNoErrors();
        $this->post('/admin/wikipedia', ['page_id' => 42])->assertSessionHasNoErrors();
        $this->assertSame(1, Article::count());
        $this->assertSame('Cá voi', Article::first()->title);
        Http::assertSent(fn ($r) => $r->hasHeader('User-Agent') && str_starts_with($r->url(), 'https://vi.wikipedia.org/'));
    }

    public function test_api_failure_does_not_create_article(): void
    {
        Http::fake(['*' => Http::response([], 503)]);
        $this->get('/admin/wikipedia?search=ca')->assertOk()->assertSee('Chưa kết nối được Wikipedia');
        $this->post('/admin/wikipedia', ['page_id' => 42])->assertSessionHasErrors();
        $this->assertSame(0, Article::count());
    }
}
