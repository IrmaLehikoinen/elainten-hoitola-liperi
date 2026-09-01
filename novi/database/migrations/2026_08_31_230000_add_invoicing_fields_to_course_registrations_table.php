<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
            $table->string('invoice_number')->nullable()->unique()->after('paid_at');
            $table->timestamp('issued_at')->nullable()->after('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'paid_at', 'invoice_number', 'issued_at']);
        });
    }
};