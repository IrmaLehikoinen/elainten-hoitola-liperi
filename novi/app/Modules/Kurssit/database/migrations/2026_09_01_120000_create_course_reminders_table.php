<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kurssit-moduulin oma muistutustaulu — ei sama kuin Lemmikkihoitolan
     * "reminders"-taulu, koska moduulien välillä ei jaeta koodia eikä dataa.
     * Käytetään Kurssit-etusivun "Muistettavaa"-listalla. Voi liittyä
     * tiettyyn kurssiin (course_id) tai olla yleinen muistilappu.
     */
    public function up(): void
    {
        Schema::create('course_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();

            $table->string('title');
            $table->date('due_at')->nullable();
            $table->dateTime('done_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('done_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['company_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reminders');
    }
};