<?php

namespace App\Http\Controllers;

use App\Models\BookingParticipant;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
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