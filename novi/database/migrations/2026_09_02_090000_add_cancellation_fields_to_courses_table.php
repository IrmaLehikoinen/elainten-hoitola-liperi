<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dateTime('registration_closed_at')->nullable()->after('max_participants');
            $table->dateTime('cancelled_at')->nullable()->after('registration_closed_at');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['registration_closed_at', 'cancelled_at']);
        });
    }
};