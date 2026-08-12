<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_login_urls_open_for_guests(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Admin Login');
    }

    public function test_admin_can_login_with_email_and_logout(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password@123'),
            'is_admin' => true,
        ]);

        $this->post('/login', [
            'username' => $admin->email,
            'password' => 'Password@123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get('/admin')->assertRedirect(route('admin.dashboard'));
        $this->get('/admin/dashboard')->assertOk()->assertSee('Authenticated');
        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_non_admin_cannot_login(): void
    {
        $user = User::factory()->create([
            'username' => 'customer',
            'password' => Hash::make('Password@123'),
            'is_admin' => false,
        ]);

        $this->post('/login', [
            'username' => $user->username,
            'password' => 'Password@123',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_admin_can_change_password_and_is_logged_out(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('Password@123'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin)->post('/admin/change-password', [
            'current_password' => 'Password@123',
            'password' => 'Changed@456',
            'password_confirmation' => 'Changed@456',
        ])->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertTrue(Hash::check('Changed@456', $admin->fresh()->password));
    }
}
