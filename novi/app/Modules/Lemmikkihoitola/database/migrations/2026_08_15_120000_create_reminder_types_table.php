<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Muistutustyypit per yritys. Korvaa aiemmin kovakoodatun listan
     * (lääke, ruokinta, pesu, kynnet, eläinlääkäri, ulkoilutus, muu),
     * jotta jokainen hoitola voi määrittää omat muistutustyyppinsä
     * Yritysasetukset-sivulta.
     */
    public function up(): void
    {
        Schema::create('reminder_types', function (Blueprint $table) {
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
        Schema::dropIfExists('reminder_types');
    }
};