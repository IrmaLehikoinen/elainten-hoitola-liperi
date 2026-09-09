<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatment_availability_rules', function (Blueprint $table) {
            $table->date('reminder_date')->nullable();
        });

        Schema::table('treatment_special_openings', function (Blueprint $table) {
            $table->date('reminder_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('treatment_availability_rules', function (Blueprint $table) {
            $table->dropColumn('reminder_date');
        });

        Schema::table('treatment_special_openings', function (Blueprint $table) {
            $table->dropColumn('reminder_date');
        });
    }
};