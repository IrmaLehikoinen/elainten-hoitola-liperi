<?php

namespace App\Modules\Ajanvaraus\Console\Commands;

use App\Modules\Ajanvaraus\Mail\TreatmentAppointmentExpired;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CancelExpiredTreatmentAppointments extends Command
{
    protected $signature = 'ajanvaraus:cancel-expired-appointments';
    protected $description = 'Peruuttaa hoitovaraukset joiden maksun määräaika on mennyt umpeen ilman maksua';

    public function handle(): int
    {
        $expired = TreatmentAppointment::with('treatment')
            ->where('status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->whereNull('paid_at')
            ->get();

        foreach ($expired as $appointment) {
            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'payment_expired',
            ]);

            if ($appointment->email) {
                Mail::to($appointment->email)->send(new TreatmentAppointmentExpired($appointment));
            }
        }

        $this->info($expired->count().' varausta peruttu.');

        return self::SUCCESS;
    }
}