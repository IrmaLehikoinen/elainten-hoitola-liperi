<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->json('active_modules')->nullable()->after('industry');
        });

        // Täytetään olemassa olevat yritykset niiden nykyisen toimialan mukaan,
        // jotta mikään ei muutu vanhoille yrityksille.
        DB::table('companies')->whereNotNull('industry')->get()->each(function ($company) {
            DB::table('companies')
                ->where('id', $company->id)
                ->update(['active_modules' => json_encode([$company->industry])]);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('active_modules');
        });
    }
};