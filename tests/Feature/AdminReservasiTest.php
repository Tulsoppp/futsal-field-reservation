<?php

namespace Tests\Feature;

use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_terima_reservasi()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user = User::factory()->create();
        $reservasi = Reservasi::create([
            'id_user' => $user->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'total_harga' => 200000,
            'status' => 'pending',
        ]);

        $response = $this->post("/admin/reservasi/{$reservasi->id}/terima");
        
        $response->assertRedirect();
        $this->assertDatabaseHas('reservasi', [
            'id' => $reservasi->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_admin_can_tolak_reservasi()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user = User::factory()->create();
        $reservasi = Reservasi::create([
            'id_user' => $user->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'total_harga' => 200000,
            'status' => 'pending',
        ]);

        $response = $this->post("/admin/reservasi/{$reservasi->id}/tolak");
        
        $response->assertRedirect();
        $this->assertDatabaseHas('reservasi', [
            'id' => $reservasi->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_admin_can_selesaikan_reservasi()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user = User::factory()->create();
        $reservasi = Reservasi::create([
            'id_user' => $user->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'total_harga' => 200000,
            'status' => 'disetujui',
        ]);

        $response = $this->post("/admin/reservasi/{$reservasi->id}/selesai");
        
        $response->assertRedirect();
        $this->assertDatabaseHas('reservasi', [
            'id' => $reservasi->id,
            'status' => 'selesai',
        ]);
    }
}
