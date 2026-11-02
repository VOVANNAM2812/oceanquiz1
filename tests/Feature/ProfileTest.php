<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_can_be_viewed_and_updated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/profile')->assertOk();
        $this->patch('/profile', ['name' => 'Ocean User', 'email' => $user->email])->assertSessionHasNoErrors();
        $this->assertSame('Ocean User', $user->fresh()->name);
    }

    public function test_account_deletion_is_disabled_to_preserve_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertStatus(405);
        $this->assertNotNull($user->fresh());
    }
}
