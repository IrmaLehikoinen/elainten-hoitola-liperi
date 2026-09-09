<?php

namespace App\Modules\Lemmikkihoitola\Console\Commands;

use App\Modules\Lemmikkihoitola\Mail\BookingPaymentReminder;
use App\Modules\Lemmikkihoitola\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'bookings:send-payment-reminders';
    protected $description = 'Lähettää maksumuistutuksen varauksista joiden maksun määräaika on tänään';

    public function handle(): int
    {
        $dueToday = Booking::with('customer')
            ->where('status', 'pending')
            ->whereNotNull('payment_deadline')
            ->whereDate('payment_deadline', now()->toDateString())
            ->whereNull('deposit_paid_at')
            ->whereNull('payment_reminder_sent_at')
            ->get();

        foreach ($dueToday as $booking) {
            if ($booking->customer && $booking->customer->email) {
                Mail::to($booking->customer->email)->send(new BookingPaymentReminder($booking));
            }

            $booking->update(['payment_reminder_sent_at' => now()]);
        }

        $this->info($dueToday->count() . ' maksumuistutusta lähetetty.');

        return self::SUCCESS;
    }
}