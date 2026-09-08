<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Geneerinen muistutus/tehtävä-taulu.
     *
     * Käytetään etusivun "Tänään huomioitavaa" -listalla, eläinkortin
     * "Hoitojakson muistutukset" -osiossa ja kalenterissa. Yksi rivi
     * riittää kaikkiin kolmeen paikkaan - ei kirjoiteta moneen tauluun.
     *
     * type-kentän mahdolliset arvot ohjataan config/industries.php:stä
     * toimialan mukaan (lääke, ruokinta, pesu, kynnet, eläinlääkäri,
     * ulkoilutus, saapuu, lähtee, muu). custom-tyyppi sallii aina
     * vapaan otsikon riippumatta toimialasta.
     */
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->foreignId('booking_participant_id')->nullable()
                ->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->nullable()
                ->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->string('type');
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->dateTime('due_at');
            $table->dateTime('done_at')->nullable();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->foreignId('done_by')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['company_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};