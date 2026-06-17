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
            $table->boolean('membership_free_hour_used')->default(false)->after('membership_expires_at');
            $table->timestamp('membership_last_booking_at')->nullable()->after('membership_free_hour_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_free_hour_used', 'membership_last_booking_at']);
        });
    }
};
