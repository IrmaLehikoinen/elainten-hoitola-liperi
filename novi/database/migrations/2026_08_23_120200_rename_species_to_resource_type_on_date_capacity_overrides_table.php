<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('date_capacity_overrides', function (Blueprint $table) {
            $table->renameColumn('species', 'resource_type');
        });
    }

    public function down(): void
    {
        Schema::table('date_capacity_overrides', function (Blueprint $table) {
            $table->renameColumn('resource_type', 'species');
        });
    }
};