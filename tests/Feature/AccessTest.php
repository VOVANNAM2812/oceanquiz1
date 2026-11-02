<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_are_protected_and_admin_can_open_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $user->role = 'admin';
        $user->save();
        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_registration_cannot_escalate_role(): void
    {
        $this->post('/register', ['name' => 'Explorer', 'email' => 'new@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'role' => 'admin'])->assertRedirect('/home');
        $this->assertSame('user', User::where('email', 'new@example.com')->firstOrFail()->role);
    }

    public function test_admin_login_goes_to_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/admin');
    }
}
