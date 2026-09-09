<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Events\CollectExternalCalendarEntries;
use App\Http\Controllers\Controller;
use App\Modules\Ajanvaraus\Models\CalendarBlock;
use App\Models\Company;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $calendarAnchor = $request->query('date')
            ? Carbon::createFromFormat('Y-m-d', $request->query('date'))->startOfDay()
            : Carbon::today();

        $calendarView = in_array($request->query('view'), ['month', 'week', 'day'], true) ? $request->query('view') : 'month';

        if ($calendarView === 'day') {
            $monthStart = $calendarAnchor->copy();
            $monthEnd = $calendarAnchor->copy();
        } elseif ($calendarView === 'week') {
            $monthStart = $calendarAnchor->copy()->startOfWeek(Carbon::MONDAY);
            $monthEnd = $calendarAnchor->copy()->endOfWeek(Carbon::SUNDAY);
        } else {
            $monthStart = $calendarAnchor->copy()->startOfMonth();
            $monthEnd = $calendarAnchor->copy()->endOfMonth();
        }

        $appointments = TreatmentAppointment::with('treatment')
            ->whereBetween('starts_at', [$monthStart, $monthEnd->copy()->endOfDay()])
            ->where('status', '!=', 'cancelled')
            ->get();

        $event = new CollectExternalCalendarEntries($monthStart, $monthEnd->copy()->endOfDay(), 'ajanvaraus');
        Event::dispatch($event);

        $treatments = Treatment::with(['availabilityRules', 'specialOpenings'])->where('is_active', true)->get();

        $blocks = CalendarBlock::whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])->get();

        $blockColor = '#3F4F3A';

        $days = []; 

        for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
            $dayAppointments = $appointments->filter(fn ($a) => $a->starts_at->isSameDay($date))->values();
            $bookedTreatmentIds = $dayAppointments->pluck('treatment_id')->unique();

            $entries = collect();

            $dayAppointments
                ->groupBy(fn ($a) => $a->treatment_id.'|'.$a->starts_at->format('H:i'))
                ->each(function ($group) use ($entries) {
                    $treatment = $group->first()->treatment;
                    $title = $treatment->name.' '.$group->first()->starts_at->format('H:i');

                    if ($treatment->capacity > 1) {
                        $title .= ' ('.$group->count().'/'.$treatment->capacity.')';
                    }

                    $entries->push([
                        'title' => $title,
                        'color' => $treatment->color ?? '#999',
                        'filled' => true,
                        'href' => route('ajanvaraus.treatments.edit', $treatment->id),
                    ]);
                });

            foreach ($treatments as $treatment) {
                if ($bookedTreatmentIds->contains($treatment->id)) {
                    continue;
                }

                foreach ($treatment->availabilityRules as $rule) {
                    if ((int) $rule->weekday === $date->dayOfWeek) {
                        $entries->push([
                            'title' => $treatment->name.' '.substr($rule->start_time, 0, 5),
                            'color' => $treatment->color ?? '#999',
                            'filled' => false,
                            'href' => route('ajanvaraus.treatments.edit', $treatment->id),
                        ]);
                        break;
                    }
                }

                foreach ($treatment->specialOpenings as $opening) {
                    if (Carbon::parse($opening->date)->isSameDay($date)) {
                        $entries->push([
                            'title' => $treatment->name.' '.substr($opening->start_time, 0, 5),
                            'color' => $treatment->color ?? '#999',
                            'filled' => false,
                            'href' => route('ajanvaraus.treatments.edit', $treatment->id),
                        ]);
                        break;
                    }
                }
            }

            $external = collect($event->entries)->filter(fn ($e) => $e['date'] === $date->format('Y-m-d'))->values();

            $dayBlocks = $blocks->filter(fn ($b) => $b->date->isSameDay($date))->values();

            $dayBlocks
                ->groupBy(function ($block) {
                    $label = preg_replace('/\s*\(varaus #\d+\)\s*$/', '', $block->reason ?: 'Ei vapaita aikoja');

                    return $label.'|'.$block->start_time.'|'.$block->end_time;
                })
                ->each(function ($group) use ($entries, $blockColor) {
                    $first = $group->first();
                    $title = preg_replace('/\s*\(varaus #\d+\)\s*$/', '', $first->reason ?: 'Ei vapaita aikoja');

                    if ($first->start_time) {
                        $title .= ' klo '.substr($first->start_time, 0, 5);

                        if ($first->end_time && $first->end_time !== $first->start_time) {
                            $title .= '–'.substr($first->end_time, 0, 5);
                        }
                    }

                    if ($group->count() > 1) {
                        $title .= ' ('.$group->count().')';
                    }

                    $entries->push([
                        'title' => $title,
                        'color' => $blockColor,
                        'filled' => true,
                    ]);
                });

            $days[] = [
                'date' => $date->copy(),
                'entries' => $entries->concat($external)->sortBy(function ($entry) {
                    if (preg_match('/(\d{2}:\d{2})/', $entry['title'], $m)) {
                        return $m[1];
                    }

                    return '99:99';
                })->values(),
            ];
        }

        return view('ajanvaraus::calendar.index', [
            'calendarMonth' => $monthStart,
            'calendarDays' => $days,
            'calendarView' => $calendarView,
            'calendarAnchor' => $calendarAnchor,
        ]);
    }

    public function day(Request $request)
    {
        $date = Carbon::createFromFormat('Y-m-d', $request->query('date', now()->format('Y-m-d')))->startOfDay();

        $appointments = TreatmentAppointment::with('treatment')
            ->whereBetween('starts_at', [$date, $date->copy()->endOfDay()])
            ->where('status', '!=', 'cancelled')
            ->orderBy('starts_at')
            ->get();

        $bookedTreatmentIds = $appointments->pluck('treatment_id')->unique();

        $event = new CollectExternalCalendarEntries($date, $date->copy()->endOfDay(), 'ajanvaraus');
        Event::dispatch($event);

        $blocks = CalendarBlock::whereDate('date', $date->toDateString())->get();

        $blockColor = '#3F4F3A';

        $allTreatments = Treatment::with(['availabilityRules', 'specialOpenings'])->where('is_active', true)->orderBy('name')->get();

        $recurring = collect();

        foreach ($allTreatments as $treatment) {
            if ($bookedTreatmentIds->contains($treatment->id)) {
                continue;
            }

            foreach ($treatment->availabilityRules as $rule) {
                if ((int) $rule->weekday === $date->dayOfWeek) {
                    $recurring->push([
                        'treatment' => $treatment,
                        'title' => $treatment->name.' klo '.substr($rule->start_time, 0, 5).'–'.substr($rule->end_time, 0, 5),
                    ]);
                    break;
                }
            }

            foreach ($treatment->specialOpenings as $opening) {
                if (Carbon::parse($opening->date)->isSameDay($date)) {
                    $recurring->push([
                        'treatment' => $treatment,
                        'title' => $treatment->name.' klo '.substr($opening->start_time, 0, 5).'–'.substr($opening->end_time, 0, 5),
                    ]);
                    break;
                }
            }
        }

        return view('ajanvaraus::calendar.day', [
            'date' => $date,
            'appointments' => $appointments,
            'external' => $event->entries,
            'recurring' => $recurring,
            'treatments' => $allTreatments,
            'blocks' => $blocks,
            'blockColor' => $blockColor,
        ]);
    }
}