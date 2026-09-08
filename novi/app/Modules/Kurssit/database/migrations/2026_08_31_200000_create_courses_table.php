<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // 'text' = kirjoitettu kuvaus, 'brochure' = ladattu esitetiedosto
            $table->string('presentation_type')->default('text');
            $table->longText('description_html')->nullable();
            $table->string('brochure_path')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->unsignedInteger('max_participants')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};