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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lapangan');
            $table->unsignedBigInteger('harga_per_jam');
            $table->string('status')->default('tersedia');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();

            // proses untuk mempercepat query database pada kolom tanggal, jam_mulai, dan jam_selesai
            $table->index(['tanggal', 'jam_mulai', 'jam_selesai']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
