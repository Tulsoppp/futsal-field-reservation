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
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_type')->nullable(); // basic, pro, elite
            $table->string('membership_status')->nullable(); // pending, active, rejected
            $table->string('membership_proof')->nullable(); // path to payment proof
            $table->timestamp('membership_expires_at')->nullable(); // expiration date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_type', 'membership_status', 'membership_proof', 'membership_expires_at']);
        });
    }
};
