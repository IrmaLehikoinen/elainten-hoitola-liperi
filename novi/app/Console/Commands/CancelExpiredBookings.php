<?php

namespace App\Console\Commands;

use App\Mail\BookingPaymentExpired;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';
    protected $description = 'Peruuttaa varaukset joiden ennakkomaksun määräaika on mennyt umpeen ilman maksua';

    public function handle(): int
    {
        $expired = Booking::with('customer')
            ->where('status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->whereNull('deposit_paid_at')
            ->get();

        foreach ($expired as $booking) {
            $booking->update(['status' => 'cancelled']);

            if ($booking->customer && $booking->customer->email) {
                Mail::to($booking->customer->email)->send(new BookingPaymentExpired($booking));
            }
        }

        $this->info($expired->count() . ' varausta peruttu.');

        return self::SUCCESS;
    }
}