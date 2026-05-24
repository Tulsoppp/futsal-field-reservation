<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->date('tanggal'); // Tanggal booking lapangan
            $table->time('jam_mulai'); // Jam mulai booking (misal: 19:00:00)
            $table->time('jam_selesai'); // Jam selesai booking
            $table->unsignedBigInteger('total_harga');
            $table->string('status')->default('menunggu');  // status reservasi: menunggu, dibayar, dibatalkan
            $table->string('metode_pembayaran')->nullable(); // untuk menyimpan metode pembayaran yang dipilih oleh pengguna, misalnya 'transfer bank', 'e-wallet', dll.
            $table->string('bukti_pembayaran')->nullable(); // untuk menyimpan nama file bukti pembayaran yang diunggah oleh pengguna
            $table->string('catatan')->nullable(); // untuk menyimpan catatan tambahan dari pengguna, misalnya permintaan khusus atau informasi tambahan lainnya    
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasis');
    }
};
