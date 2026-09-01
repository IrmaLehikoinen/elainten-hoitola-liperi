<?php

namespace App\Modules\Kurssit\Console\Commands;

use App\Modules\Kurssit\Mail\CourseRegistrationExpired;
use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CancelExpiredCourseRegistrations extends Command
{
    protected $signature = 'kurssit:cancel-expired';
    protected $description = 'Peruuttaa kurssi-ilmoittautumiset joiden maksun määräaika on mennyt umpeen ilman maksua';

    public function handle(): int
    {
        $expired = CourseRegistration::with('course')
            ->where('status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->whereNull('paid_at')
            ->get();

        foreach ($expired as $registration) {
            $registration->update(['status' => 'cancelled']);

            if ($registration->email) {
                Mail::to($registration->email)->send(new CourseRegistrationExpired($registration));
            }
        }

        $this->info($expired->count().' ilmoittautumista peruttu.');

        return self::SUCCESS;
    }
}