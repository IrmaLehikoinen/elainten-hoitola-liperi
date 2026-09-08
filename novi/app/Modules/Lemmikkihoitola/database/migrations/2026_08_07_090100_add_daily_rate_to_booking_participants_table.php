<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eläinkohtainen vuorokausihinta, jotta kuitilla voidaan eritellä
     * jokaisen eläimen hoito-osuus erikseen (hoitopäivät x vrk-hinta).
     */
    public function up(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {
            $table->decimal('daily_rate', 8, 2)->nullable()->after('resource_id');
        });
    }

    public function down(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {
            $table->dropColumn('daily_rate');
        });
    }
};