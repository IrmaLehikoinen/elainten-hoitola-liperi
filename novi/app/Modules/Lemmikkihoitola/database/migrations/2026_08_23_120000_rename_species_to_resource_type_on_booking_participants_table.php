<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {
            $table->renameColumn('species', 'resource_type');
        });
    }

    public function down(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {
            $table->renameColumn('resource_type', 'species');
        });
    }
};