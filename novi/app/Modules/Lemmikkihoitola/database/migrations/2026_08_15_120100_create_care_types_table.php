<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hoitomuodot per yritys (esim. puolipäivä, kokopäivä, yöhoito).
     * Korvaa aiemmin kovakoodatun listan Booking.care_type-kentälle,
     * jotta jokainen hoitola voi määrittää omat hoitomuotonsa
     * Yritysasetukset-sivulta.
     */
    public function up(): void
    {
        Schema::create('care_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_types');
    }
};