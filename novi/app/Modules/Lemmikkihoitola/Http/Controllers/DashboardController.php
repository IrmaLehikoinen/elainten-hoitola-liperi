<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lemmikkihoitola\Models\BookingParticipant;
use App\Modules\Lemmikkihoitola\Models\Reminder;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
            $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();

            $calendarAnchor = request('date')
            ? Carbon::createFromFormat('Y-m-d', request('date'))->startOfDay()
            : $today->copy();
        $monthStart = $calendarAnchor->copy()->startOfMonth(); 

        $newBookingsCount = \App\Modules\Lemmikkihoitola\Models\Booking::where('confirmation_channel', 'online')
            ->whereNull('acknowledged_at')
            ->where('status', 'confirmed')
            ->count();

        $firstNewBookingDate = \App\Modules\Lemmikkihoitola\Models\Booking::where('confirmation_channel', 'online')
            ->whereNull('acknowledged_at')
            ->where('status', 'confirmed')
            ->orderBy('start_date')
            ->value('start_date');

        $firstNewBookingDate = $firstNewBookingDate ? Carbon::parse($firstNewBookingDate)->format('Y-m-d') : null;

        return view('dashboard', [
            'today' => $today,
            'todayEvents' => $this->buildTodayEvents($today),
            'todayReminders' => $this->buildTodayReminders($today),
            'inCareToday' => $this->participantsInCareOn($today),
            'arrivingTomorrow' => $this->participantsArrivingOn($tomorrow),
            'calendarMonth' => $monthStart,
            'calendarDays' => $this->buildCalendarDays($monthStart),
            'newBookingsCount' => $newBookingsCount,
            'firstNewBookingDate' => $firstNewBookingDate,
        ]);
    }

    private function participantsInCareOn(Carbon $date)
    {
        return BookingParticipant::with(['booking.customer', 'pet'])
            ->whereHas('booking', fn ($q) => $q->where('status', 'confirmed'))
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get()
            ->sortBy(fn ($p) => optional($p->booking)->pickup_at)
            ->values();
    }

    private function participantsArrivingOn(Carbon $date)
    {
        return BookingParticipant::with(['booking.customer', 'pet'])
            ->whereHas('booking', fn ($q) => $q->where('status', 'confirmed'))
            ->whereDate('start_date', $date)
            ->get()
            ->sortBy(fn ($p) => optional($p->booking)->arrival_at)
            ->values();
    }

    /**
     * "Saapuvat ja lähtevät tänään" - pelkät saapumiset ja lähdöt,
     * ei muistutuksia. Pelkkä tietolista, ei rastitusta.
     */
    private function buildTodayEvents(Carbon $today)
    {
        $items = collect();

            foreach ($this->participantsArrivingOn($today) as $participant) {
            $items->push([
                'time' => optional($participant->booking)->arrival_at?->format('H:i') ?? '--:--',
                'label' => 'Saapuu tänään',
                'name' => $participant->name,
                'species' => $participant->resource_type,
                'customer' => optional(optional($participant->booking)->customer)->name,
                'pet_id' => $participant->pet_id,
                'customer_id' => optional($participant->booking)->customer_id,
            ]);
        }

        $leavingToday = BookingParticipant::with(['booking.customer', 'pet'])
            ->whereHas('booking', fn ($q) => $q->where('status', 'confirmed'))
            ->whereDate('end_date', $today)
            ->get();

        foreach ($leavingToday as $participant) {
            $items->push([
                'time' => optional($participant->booking)->pickup_at?->format('H:i') ?? '--:--',
                'label' => 'Lähtee tänään',
                'name' => $participant->name,
                'species' => $participant->resource_type,
                'customer' => optional(optional($participant->booking)->customer)->name,
                'pet_id' => $participant->pet_id,
                'customer_id' => optional($participant->booking)->customer_id,
            ]);
        }
        return $items->sortBy('time')->values();
    }

    /**
     * "Muistutukset" - tämän päivän avoimet (ei vielä tehdyt) muistutukset.
     * Kun rasti laitetaan, rivi poistuu listalta (ei vain harmaannu).
     */
    private function buildTodayReminders(Carbon $today)
    {
        $reminders = Reminder::with(['pet', 'bookingParticipant.booking.customer', 'customer'])
            ->forDate($today)
            ->open()
            ->onlyConfirmedBooking()
            ->get();

        return $reminders->map(function ($reminder) {
            return [
                'id' => $reminder->id,
                'time' => $reminder->due_at->format('H:i'),
                'label' => $reminder->title ?: self::typeLabel($reminder->type),
                'name' => optional($reminder->pet)->name
                    ?? optional($reminder->bookingParticipant)->name,
                'customer' => optional($reminder->customer)->name
                    ?? optional(optional(optional($reminder->bookingParticipant)->booking)->customer)->name,
                'pet_id' => $reminder->pet_id
                    ?? optional($reminder->bookingParticipant)->pet_id,
            ];
        })->sortBy('time')->values();
    }

    public static function typeLabel(string $type): string
    {
        $reminderType = \App\Modules\Lemmikkihoitola\Models\ReminderType::where('slug', $type)->first();

        return $reminderType?->label ?? 'Muu tehtävä';
    }

        private function buildCalendarDays(Carbon $monthStart)
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        $participants = BookingParticipant::with(['pet', 'booking'])
            ->whereHas('booking', fn ($q) => $q->where('status', 'confirmed'))
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->get();

            $capacities = \App\Models\Resource::query()
            ->get(['type', 'capacity'])
            ->groupBy(fn ($resource) => mb_strtolower(trim($resource->type)))
            ->map(fn ($group) => (int) $group->sum('capacity'))
            ->all();

        $days = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayParticipants = $participants->filter(
                fn ($p) => $p->start_date->lte($date) && $p->end_date->gte($date)
            )->values();

                $speciesCounts = $dayParticipants
                ->groupBy(fn ($p) => mb_strtolower(trim($p->resource_type)))
                ->map(fn ($group) => $group->count());

            $isFull = $speciesCounts->some(
                fn ($count, $species) => $count >= ($capacities[$species] ?? PHP_INT_MAX)
            );

            $newParticipant = $dayParticipants->first(
                fn ($p) => $p->booking
                    && $p->booking->confirmation_channel === 'online'
                    && is_null($p->booking->acknowledged_at)
            );

            $days[] = [
                'date' => $date->copy(),
                'count' => $dayParticipants->count(),
                'participants' => $dayParticipants,
                'isFull' => $isFull,
                'has_new' => (bool) $newParticipant,
                'new_booking_id' => $newParticipant?->booking_id,
            ];
        }

        return $days;
    }
}