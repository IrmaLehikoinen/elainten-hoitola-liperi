<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Yksi kuitti per varaus (vaikka varauksessa olisi useita eläimiä).
     * line_items jäädyttää eläinkohtaisen erittelyn (hoitopäivät +
     * lisäpalvelut) sellaisena kuin se oli hoidon päättyessä, jotta
     * kuitti ei muutu jälkikäteen vaikka palveluhintoja päivitettäisiin.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->string('invoice_number')->unique();

            // Eläinkohtainen erittely: [{participant_id, name, care_days,
            // daily_rate, care_subtotal, services: [{name, price}]}, ...]
            $table->json('line_items');

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('total_due', 10, 2)->default(0);

            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};