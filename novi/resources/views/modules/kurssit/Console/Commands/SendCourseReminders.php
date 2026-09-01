<?php

namespace App\Modules\Kurssit\Console\Commands;

use App\Modules\Kurssit\Mail\CourseReminderTomorrow;
use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCourseReminders extends Command
{
    protected $signature = 'kurssit:send-reminders';
    protected $description = 'Lähettää muistutussähköpostin osallistujille, joiden kurssi alkaa huomenna';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $registrations = CourseRegistration::with('course')
            ->whereNull('reminder_sent_at')
            ->where('status', 'confirmed')
            ->whereHas('course', function ($query) use ($tomorrow) {
                $query->whereDate('starts_at', $tomorrow);
            })
            ->get();

        foreach ($registrations as $registration) {
            if (! $registration->email) {
                continue;
            }

            Mail::to($registration->email)->send(new CourseReminderTomorrow($registration));
            $registration->update(['reminder_sent_at' => now()]);
        }

        $this->info($registrations->count().' muistutusta lähetetty.');

        return self::SUCCESS;
    }
}