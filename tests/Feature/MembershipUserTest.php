<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_membership()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/membership/register');

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'membership_status' => 'pending',
        ]);
    }

    public function test_user_can_view_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/profile');
        $response->assertStatus(200);
    }
}
