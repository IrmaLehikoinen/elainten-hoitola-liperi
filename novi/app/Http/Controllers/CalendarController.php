<?php

namespace App\Http\Controllers;

use App\Models\BookingParticipant;
use App\Models\DateCapacityOverride;
use App\Services\AvailabilityService;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    /**
     * Kalenterisivun kuukausinäkymä - sama oikea data kuin etusivulla,
     * ei enää kovakoodattua testidataa.
     */
   public function index()
    {
        $view = in_array(request('view'), ['month', 'week', 'day'], true) ? request('view') : 'month';

        $anchor = request('date')
            ? Carbon::createFromFormat('Y-m-d', request('date'))->startOfDay()
            : today();

        if ($view === 'day') {
            $periodStart = $anchor->copy();
            $periodEnd = $anchor->copy();
            $periodLabel = ucfirst($anchor->translatedFormat('l j.n.Y'));
        } elseif ($view === 'week') {
            $periodStart = $anchor->copy()->startOfWeek(Carbon::MONDAY);
            $periodEnd = $anchor->copy()->endOfWeek(Carbon::SUNDAY);
            $periodLabel = $periodStart->translatedFormat('j.n.').' – '.$periodEnd->translatedFormat('j.n.Y');
        } else {
            $periodStart = $anchor->copy()->startOfMonth();
            $periodEnd = $anchor->copy()->endOfMonth();
            $periodLabel = ucfirst($periodStart->translatedFormat('F Y'));
        }

        $participants = BookingParticipant::with(['pet', 'booking'])
            ->whereDate('start_date', '<=', $periodEnd)
            ->whereDate('end_date', '>=', $periodStart)
            ->get();

        $days = [];

        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            $dayParticipants = $participants->filter(
                fn ($p) => $p->start_date->lte($date) && $p->end_date->gte($date)
            )->values();

            $days[] = [
                'date' => $date->copy(),
                'count' => $dayParticipants->count(),
                'participants' => $dayParticipants,
            ];
        }

        return view('calendar.index', [
            'calendarView' => $view,
            'anchorDate' => $anchor,
            'periodStart' => $periodStart,
            'periodLabel' => $periodLabel,
            'calendarDays' => $days,
            'careTypes' => \App\Models\CareType::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Päivänäkymä: kaikki kyseisen päivän varaukset ryhmiteltynä
     * varauksen mukaan. Yhden eläimen varaus linkittää suoraan
     * eläinkorttiin, useamman eläimen varaus (sama asiakas) linkittää
     * asiakaskorttiin, josta eläinkortit avataan yksitellen.
     */
       public function day(string $date, AvailabilityService $availability)
    {
        $day = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();

        $participants = BookingParticipant::with(['booking.customer', 'pet'])
            ->whereDate('start_date', '<=', $day)
            ->whereDate('end_date', '>=', $day)
            ->get()
            ->each(function ($participant) use ($day) {
                $participant->is_arriving_today = $participant->start_date->isSameDay($day);
                $participant->is_leaving_today = $participant->end_date->isSameDay($day);
            });

            $bookings = $participants
            ->groupBy('booking_id')
            ->map(function ($group) {
                $booking = $group->first()->booking;

                return [
                    'customer' => optional($booking)->customer,
                    'participants' => $group->values(),
                ];
            })
            ->values();

        $arrivingToday = $participants->filter(fn ($p) => $p->is_arriving_today)->values();
        $leavingToday = $participants->filter(fn ($p) => $p->is_leaving_today)->values();

        $reminders = \App\Models\Reminder::with(['pet', 'bookingParticipant.booking.customer', 'customer'])
            ->forDate($day)
            ->get();

         $overrides = DateCapacityOverride::whereDate('date', $day->toDateString())->get();
        $dayBlockOverride = $overrides->first(fn ($o) => $o->species === null && (int) $o->capacity === 0);

        return view('calendar.day', [
            'day' => $day,
            'bookings' => $bookings,
            'arrivingToday' => $arrivingToday,
            'leavingToday' => $leavingToday,
            'reminders' => $reminders,
            'usage' => $availability->usageForDate($day),
            'overrides' => $overrides,
            'isDayBlocked' => (bool) $dayBlockOverride,
            'dayBlockOverride' => $dayBlockOverride,
        ]);
    } 
}