<?php

namespace App\Modules\Kurssit\Console\Commands;

use App\Modules\Kurssit\Mail\CourseReminderTomorrow;
use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCourseReminders extends Command
{
    protected $signature = 'kurssit:send-reminders';
    protected $description = 'Lähettää muistutussähköpostin osallistujille jotka ilmoittautuivat ennen kuin kurssiin oli enää 24 tuntia, kun se raja on nyt saavutettu';

    public function handle(): int
    {
        $registrations = CourseRegistration::with('course')
            ->whereNull('reminder_sent_at')
            ->where('status', 'confirmed')
            ->whereHas('course', fn ($query) => $query->whereNotNull('starts_at'))
            ->get()
            ->filter(function ($registration) {
                $course = $registration->course;

                if (! $course || ! $course->starts_at) {
                    return false;
                }

                $reminderDueAt = $course->starts_at->copy()->subHours(24);

                return now()->gte($reminderDueAt)
                    && $registration->created_at->lte($reminderDueAt)
                    && now()->lt($course->starts_at);
            });

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