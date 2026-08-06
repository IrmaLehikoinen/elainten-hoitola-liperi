<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_history_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pet_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('booking_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('entry_date');

            $table->dateTime('stay_start')->nullable();
            $table->dateTime('stay_end')->nullable();

            $table->string('category')->default('general');

            $table->text('notes');

            $table->timestamps();

            $table->index(['pet_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_history_entries');
    }
};