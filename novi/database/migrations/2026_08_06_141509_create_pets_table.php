<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('sex')->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->string('microchip_number')->nullable();

            $table->text('vaccinations')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('feeding_instructions')->nullable();
            $table->text('behaviour_notes')->nullable();

            $table->string('veterinarian_name')->nullable();
            $table->string('veterinarian_phone')->nullable();

            $table->text('emergency_notes')->nullable();
            $table->text('general_notes')->nullable();

            $table->timestamps();

            $table->index(['customer_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};