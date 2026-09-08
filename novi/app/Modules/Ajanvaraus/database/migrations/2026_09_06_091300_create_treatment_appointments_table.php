<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status')->default('confirmed');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_choice')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('payment_deadline')->nullable();
            $table->string('payment_token')->nullable()->unique();
            $table->string('cancellation_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_appointments');
    }
};