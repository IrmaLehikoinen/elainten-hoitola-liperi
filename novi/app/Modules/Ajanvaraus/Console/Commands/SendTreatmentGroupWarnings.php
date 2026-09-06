<?php

namespace App\Modules\Ajanvaraus\Console\Commands;

use App\Modules\Ajanvaraus\Mail\GroupSessionAtRisk;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTreatmentGroupWarnings extends Command
{
    protected $signature = 'ajanvaraus:send-group-warnings';
    protected $description = 'Lähettää varoitusviestin ryhmän osallistujille jos minimiosallistujamäärä uhkaa jäädä täyttymättä';

    public function handle(): int
    {
        $sentCount = 0;

        $treatments = Treatment::whereNotNull('min_participants')
            ->whereNotNull('warning_days_before')
            ->get();

        foreach ($treatments as $treatment) {
            $targetDate = now()->addDays($treatment->warning_days_before)->startOfDay();

            $groups = TreatmentAppointment::where('treatment_id', $treatment->id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('starts_at', [$targetDate, $targetDate->copy()->endOfDay()])
                ->get()
                ->groupBy(fn ($appointment) => $appointment->starts_at->toDateTimeString());

            foreach ($groups as $appointments) {
                if ($appointments->contains(fn ($a) => $a->warning_sent_at !== null)) {
                    continue;
                }

                $count = $appointments->count();

                if ($count >= $treatment->min_participants) {
                    continue;
                }

                foreach ($appointments as $appointment) {
                    if (! $appointment->email) {
                        continue;
                    }

                    Mail::to($appointment->email)->send(new GroupSessionAtRisk($appointment, $count, $treatment->min_participants));
                    $sentCount++;
                }

                $appointments->each->update(['warning_sent_at' => now()]);
            }
        }

        $this->info($sentCount.' varoitusviestiä lähetetty.');

        return self::SUCCESS;
    }
}