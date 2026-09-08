<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dateTime('arrival_at')->nullable()->after('customer_id');
            $table->dateTime('pickup_at')->nullable()->after('arrival_at');

            $table->string('care_type')
                ->default('full_day')
                ->after('pickup_at');

            $table->foreignId('responsible_user_id')
                ->nullable()
                ->after('care_type')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('resource_id')
                ->nullable()
                ->after('responsible_user_id')
                ->constrained('resources')
                ->nullOnDelete();

            $table->string('payment_status')
                ->default('unpaid')
                ->after('status');

            $table->string('priority')
                ->default('normal')
                ->after('payment_status');

            $table->string('confirmation_channel')
                ->nullable()
                ->after('priority');

            $table->decimal('price_estimate', 8, 2)
                ->default(0)
                ->after('total_price');

            $table->timestamp('locked_until')
                ->nullable()
                ->after('deposit_paid_at');

            $table->boolean('send_email_confirmation')
                ->default(false)
                ->after('locked_until');

            $table->boolean('send_sms_confirmation')
                ->default(false)
                ->after('send_email_confirmation');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['responsible_user_id']);
            $table->dropForeign(['resource_id']);

            $table->dropColumn([
                'arrival_at',
                'pickup_at',
                'care_type',
                'responsible_user_id',
                'resource_id',
                'payment_status',
                'priority',
                'confirmation_channel',
                'price_estimate',
                'locked_until',
                'send_email_confirmation',
                'send_sms_confirmation',
            ]);
        });
    }
};