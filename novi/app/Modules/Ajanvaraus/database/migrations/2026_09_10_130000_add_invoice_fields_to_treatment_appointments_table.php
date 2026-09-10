<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatment_appointments', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('payment_token');
            $table->dateTime('issued_at')->nullable()->after('invoice_number');
            $table->dateTime('refunded_at')->nullable()->after('issued_at');
        });
    }

    public function down(): void
    {
        Schema::table('treatment_appointments', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'issued_at', 'refunded_at']);
        });
    }
};