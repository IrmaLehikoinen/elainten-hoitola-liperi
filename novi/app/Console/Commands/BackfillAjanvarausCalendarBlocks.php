<?php

namespace App\Console\Commands;

use App\Events\CompanyDateClosed;
use App\Events\ExternalTimeBlocked;
use App\Models\DateCapacityOverride;
use App\Modules\Lemmikkihoitola\Models\Booking;
use App\Modules\Lemmikkihoitola\Models\Reminder;
use App\Modules\Lemmikkihoitola\Models\ReminderType;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

class BackfillAjanvarausCalendarBlocks extends Command
{
    protected $signature = 'ajanvaraus:backfill-blocks';

    protected $description = 'Kertaluonteinen ajo: luo puuttuvat CalendarBlock-rivit jo olemassa olevista suljetuista päivistä, varauksista ja muistutuksista, jotta ne näkyvät Sydänpolun kalenterissa.';

    public function handle(): int
    {
        $closedDays = DateCapacityOverride::whereNull('resource_type')->where('capacity', 0)->get();

        foreach ($closedDays as $override) {
            Event::dispatch(new CompanyDateClosed(\App\Models\Company::where('industry', 'kurssit')->value('id'), Carbon::parse($override->date), $override->note));
        }

        $this->info($closedDays->count().' suljettua päivää käsitelty.');

        $bookings = Booking::where('status', '!=', 'cancelled')->get();

        foreach ($bookings as $booking) {
            $arrivalAt = Carbon::parse($booking->arrival_at);
            $pickupAt = Carbon::parse($booking->pickup_at);

            $firstParticipant = $booking->participants()->first();
            $bookingPetUrl = ($firstParticipant && $firstParticipant->pet_id)
                ? route('admin.pets.show', $firstParticipant->pet_id, false)
                : null;

            Event::dispatch(new ExternalTimeBlocked(
                \App\Models\Company::where('industry', 'kurssit')->value('id'),
                $arrivalAt->copy(),
                $arrivalAt->format('H:i:s'),
                $arrivalAt->copy()->addMinutes(30)->format('H:i:s'),
                'Lemmikkihoitola: tuonti (varaus #'.$booking->id.')',
                $bookingPetUrl
            ));

            Event::dispatch(new ExternalTimeBlocked(
                \App\Models\Company::where('industry', 'kurssit')->value('id'),
                $pickupAt->copy(),
                $pickupAt->format('H:i:s'),
                $pickupAt->copy()->addMinutes(30)->format('H:i:s'),
                'Lemmikkihoitola: hakuaika (varaus #'.$booking->id.')',
                $bookingPetUrl
            ));
        }

        $this->info($bookings->count().' varausta käsitelty.');

        $syncedTypes = ReminderType::where('show_in_ajanvaraus_calendar', true)->pluck('slug');

        $reminders = Reminder::whereIn('type', $syncedTypes)->get();

        foreach ($reminders as $reminder) {
            $dueAt = Carbon::parse($reminder->due_at);

            Event::dispatch(new ExternalTimeBlocked(
                \App\Models\Company::where('industry', 'kurssit')->value('id'),
                $dueAt->copy(),
                $dueAt->format('H:i:s'),
                $dueAt->copy()->addMinutes(30)->format('H:i:s'),
                'Lemmikkihoitola: muistutus (#'.$reminder->id.')',
                route('admin.pets.show', $reminder->pet_id, false)
            ));
        }

        $this->info($reminders->count().' muistutusta käsitelty.');

        return self::SUCCESS;
    }
}