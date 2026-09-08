<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eläinkortin toinen muistiinpanokenttä. "general_notes" on jo
     * olemassa ja toimii asiakkaalle näkyvänä kenttänä. Tämä uusi
     * "internal_notes" on hoitolan sisäinen - asiakas ei näe sitä
     * koskaan, vaikka myöhemmin rakennettaisiin asiakkaalle oma
     * kirjautuminen.
     */
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->text('internal_notes')->nullable()->after('general_notes');
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('internal_notes');
        });
    }
};