<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->unsignedInteger('min_participants')->nullable()->after('capacity');
            $table->unsignedInteger('warning_days_before')->nullable()->after('min_participants');
        });

        Schema::table('treatment_appointments', function (Blueprint $table) {
            $table->timestamp('warning_sent_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropColumn(['min_participants', 'warning_days_before']);
        });

        Schema::table('treatment_appointments', function (Blueprint $table) {
            $table->dropColumn('warning_sent_at');
        });
    }
};