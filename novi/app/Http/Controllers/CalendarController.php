<?php

namespace App\Http\Controllers;

use App\Models\BookingParticipant;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    /**
     * Kalenterisivun kuukausinäkymä - sama oikea data kuin etusivulla,
     * ei enää kovakoodattua testidataa.
     */
    public function index()
    {
        $monthStart = today()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $participants = BookingParticipant::with(['pet', 'booking'])
            ->whereDate('start_date', '<=', $monthEnd)
            ->whereDate('end_date', '>=', $monthStart)
            ->get();

        $days = [];

        for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
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
            'calendarMonth' => $monthStart,
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
    public function day(string $date)
    {
        $day = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();

        $participants = BookingParticipant::with(['booking.customer', 'pet'])
            ->whereDate('start_date', '<=', $day)
            ->whereDate('end_date', '>=', $day)
            ->get();

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

        return view('calendar.day', [
            'day' => $day,
            'bookings' => $bookings,
        ]);
    }
}