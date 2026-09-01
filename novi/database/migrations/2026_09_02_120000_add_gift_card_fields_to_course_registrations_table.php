<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->foreignId('gift_card_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->decimal('gift_card_amount', 8, 2)->nullable()->after('gift_card_id');
            $table->dateTime('gift_card_applied_at')->nullable()->after('gift_card_amount');
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gift_card_id');
            $table->dropColumn(['gift_card_amount', 'gift_card_applied_at']);
        });
    }
};