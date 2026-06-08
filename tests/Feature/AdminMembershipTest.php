<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_terima_membership()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user = User::factory()->create([
            'membership_status' => 'pending'
        ]);

        $response = $this->post("/admin/membership/{$user->id}/terima");
        
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'membership_status' => 'active',
            'status_member' => 1
        ]);
    }

    public function test_admin_can_tolak_membership()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user = User::factory()->create([
            'membership_status' => 'pending'
        ]);

        $response = $this->post("/admin/membership/{$user->id}/tolak");
        
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'membership_status' => 'inactive',
        ]);
    }
}
