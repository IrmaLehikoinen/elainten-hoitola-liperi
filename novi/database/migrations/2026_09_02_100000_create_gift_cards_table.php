<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('source'); // 'in_person' tai 'online'
            $table->decimal('initial_amount', 8, 2);
            $table->decimal('balance', 8, 2);
            $table->date('valid_until')->nullable();
            $table->string('purchaser_name')->nullable();
            $table->string('purchaser_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};