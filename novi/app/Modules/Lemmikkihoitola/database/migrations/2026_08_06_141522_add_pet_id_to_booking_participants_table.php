<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {

            $table->foreignId('pet_id')
                ->nullable()
                ->after('booking_id')
                ->constrained('pets')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {

            $table->dropForeign(['pet_id']);
            $table->dropColumn('pet_id');

        });
    }
};