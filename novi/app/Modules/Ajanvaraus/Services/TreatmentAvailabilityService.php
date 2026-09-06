<?php

namespace App\Modules\Ajanvaraus\Services;

use App\Events\CollectExternalCalendarEntries;
use App\Modules\Ajanvaraus\Models\CalendarBlock;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

class TreatmentAvailabilityService
{
    private const BUFFER_MINUTES = 15;

    public function slotsForDate(Treatment $treatment, Carbon $date): array
    {
        $windows = $this->openWindowsForDate($treatment, $date);

        if (empty($windows)) {
            return [];
        }

        $slots = [];

        foreach ($windows as $window) {
            foreach ($this->splitIntoSlots($window, $treatment->duration_minutes) as $slot) {
                if ($this->slotIsAvailable($treatment, $slot, $date)) {
                    $slots[] = $slot;
                }
            }
        }

        return $slots;
    }

    private function openWindowsForDate(Treatment $treatment, Carbon $date): array
    {
        $windows = [];

        foreach ($treatment->availabilityRules as $rule) {
            if ((int) $rule->weekday === $date->dayOfWeek) {
                $windows[] = [
                    'start' => $date->copy()->setTimeFromTimeString($rule->start_time),
                    'end' => $date->copy()->setTimeFromTimeString($rule->end_time),
                ];
            }
        }

        foreach ($treatment->specialOpenings as $opening) {
            if (Carbon::parse($opening->date)->isSameDay($date)) {
                $windows[] = [
                    'start' => $date->copy()->setTimeFromTimeString($opening->start_time),
                    'end' => $date->copy()->setTimeFromTimeString($opening->end_time),
                ];
            }
        }

        return $windows;
    }

    private function splitIntoSlots(array $window, int $durationMinutes): array
    {
        $slots = [];
        $cursor = $window['start']->copy();

        while ($cursor->copy()->addMinutes($durationMinutes)->lte($window['end'])) {
            $slots[] = [
                'start' => $cursor->copy(),
                'end' => $cursor->copy()->addMinutes($durationMinutes),
            ];
            $cursor->addMinutes($durationMinutes);
        }

        return $slots;
    }

    private function slotIsAvailable(Treatment $treatment, array $slot, Carbon $date): bool
    {
        if ($this->isClosedByBlockOrOtherModule($treatment->company_id, $slot, $date)) {
            return false;
        }

        $conflictsWithOtherTreatment = TreatmentAppointment::where('company_id', $treatment->company_id)
            ->where('treatment_id', '!=', $treatment->id)
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '<', $slot['end']->copy()->addMinutes(self::BUFFER_MINUTES))
            ->where('ends_at', '>', $slot['start']->copy()->subMinutes(self::BUFFER_MINUTES))
            ->exists();

        if ($conflictsWithOtherTreatment) {
            return false;
        }

        $sameTreatmentBookings = TreatmentAppointment::where('company_id', $treatment->company_id)
            ->where('treatment_id', $treatment->id)
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', $slot['start'])
            ->count();

        return $sameTreatmentBookings < max(1, (int) $treatment->capacity);
    }

    private function isClosedByBlockOrOtherModule(int $companyId, array $slot, Carbon $date): bool
    {
        $blocked = CalendarBlock::where('company_id', $companyId)
            ->whereDate('date', $date->format('Y-m-d'))
            ->get()
            ->contains(function ($block) use ($date, $slot) {
                $blockStart = $block->start_time ? $date->copy()->setTimeFromTimeString($block->start_time) : $date->copy()->startOfDay();
                $blockEnd = $block->end_time ? $date->copy()->setTimeFromTimeString($block->end_time) : $date->copy()->endOfDay();

                return $blockStart->lt($slot['end']) && $blockEnd->gt($slot['start']);
            });

        if ($blocked) {
            return true;
        }

                $event = new CollectExternalCalendarEntries($date->copy()->startOfDay(), $date->copy()->endOfDay(), 'ajanvaraus');
        Event::dispatch($event);

        foreach ($event->entries as $entry) {
            if (($entry['date'] ?? null) === $date->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }
}