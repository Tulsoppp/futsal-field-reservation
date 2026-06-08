<?php

namespace Tests\Feature;

use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReservasiUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_riwayat_reservasi()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/reservasi');
        $response->assertStatus(200);
    }

    public function test_user_can_create_reservasi()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/reservasi', [
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'catatan' => 'Test Booking',
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservasi', [
            'id_user' => $user->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_user_can_pay_reservasi()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $reservasi = Reservasi::create([
            'id_user' => $user->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'total_harga' => 200000,
            'status' => 'menunggu',
        ]);

        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->post("/reservasi/{$reservasi->id}/bayar", [
            'metode_pembayaran' => 'Transfer Bank',
            'bukti_pembayaran' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservasi', [
            'id' => $reservasi->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_cancel_reservasi()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $reservasi = Reservasi::create([
            'id_user' => $user->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'total_harga' => 200000,
            'status' => 'menunggu',
        ]);

        $response = $this->post("/reservasi/{$reservasi->id}/batal", [], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservasi', [
            'id' => $reservasi->id,
            'status' => 'dibatalkan',
        ]);
    }
}
