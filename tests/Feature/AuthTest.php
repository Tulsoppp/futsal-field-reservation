<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_form()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_view_register_form()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_register()
    {
        $response = $this->post('/proses-register', [
            'nama' => 'Test User',
            'no_hp' => '081234567890',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
        ]);

        $response = $this->post('/proses-login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/reservasi');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_redirected_to_dashboard_after_login()
    {
        $admin = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/proses-login', [
            'email' => $admin->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }
}
