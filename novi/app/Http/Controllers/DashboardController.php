<?php

namespace App\Http\Controllers;

use App\Models\BookingParticipant;
use App\Models\Reminder;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();
        $monthStart = $today->copy()->startOfMonth();

        return view('dashboard', [
            'today' => $today,
            'todayItems' => $this->buildTodayList($today),
            'inCareToday' => $this->participantsInCareOn($today),
            'arrivingTomorrow' => $this->participantsArrivingOn($tomorrow),
            'calendarMonth' => $monthStart,
            'calendarDays' => $this->buildCalendarDays($monthStart),
        ]);
    }

    private function participantsInCareOn(Carbon $date)
    {
        return BookingParticipant::with(['booking.customer', 'pet'])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get()
            ->sortBy(fn ($p) => optional($p->booking)->pickup_at)
            ->values();
    }

    private function participantsArrivingOn(Carbon $date)
    {
        return BookingParticipant::with(['booking.customer', 'pet'])
            ->whereDate('start_date', $date)
            ->get()
            ->sortBy(fn ($p) => optional($p->booking)->arrival_at)
            ->values();
    }

    /**
     * "Tänään huomioitavaa" - yhdistää saapumiset, lähdöt ja
     * muistutukset-taulun rivit yhdeksi kellonajan mukaan
     * järjestetyksi listaksi.
     */
    private function buildTodayList(Carbon $today)
    {
        $items = collect();

        foreach ($this->participantsArrivingOn($today) as $participant) {
            $items->push([
                'time' => optional($participant->booking)->arrival_at?->format('H:i') ?? '--:--',
                'label' => 'Saapuu tänään',
                'name' => $participant->name,
                'customer' => optional(optional($participant->booking)->customer)->name,
                'customer_id' => optional($participant->booking)->customer_id,
                'pet_id' => $participant->pet_id,
                'reminder_id' => null,
                'done' => false,
            ]);
        }

        $leavingToday = BookingParticipant::with(['booking.customer', 'pet'])
            ->whereDate('end_date', $today)
            ->get();

        foreach ($leavingToday as $participant) {
            $items->push([
                'time' => optional($participant->booking)->pickup_at?->format('H:i') ?? '--:--',
                'label' => 'Lähtee tänään',
                'name' => $participant->name,
                'customer' => optional(optional($participant->booking)->customer)->name,
                'customer_id' => optional($participant->booking)->customer_id,
                'pet_id' => $participant->pet_id,
                'reminder_id' => null,
                'done' => false,
            ]);
        }

        $reminders = Reminder::with(['pet', 'bookingParticipant.booking.customer', 'customer'])
            ->forDate($today)
            ->get();

        foreach ($reminders as $reminder) {
            $items->push([
                'time' => $reminder->due_at->format('H:i'),
                'label' => $reminder->title ?: self::typeLabel($reminder->type),
                'name' => optional($reminder->pet)->name
                    ?? optional($reminder->bookingParticipant)->name,
                'customer' => optional($reminder->customer)->name
                    ?? optional(optional(optional($reminder->bookingParticipant)->booking)->customer)->name,
                'customer_id' => optional($reminder->customer)->id
                    ?? optional(optional($reminder->bookingParticipant)->booking)->customer_id,
                'pet_id' => $reminder->pet_id
                    ?? optional($reminder->bookingParticipant)->pet_id,
                'reminder_id' => $reminder->id,
                'done' => $reminder->isDone(),
            ]);
        }

        return $items->sortBy('time')->values();
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'medication' => 'Lääkitys',
            'feeding' => 'Ruokinta',
            'vet' => 'Eläinlääkärikäynti',
            'wash' => 'Pesu',
            'nails' => 'Kynsien leikkaus',
            'walk' => 'Ulkoilutus',
            default => 'Muu tehtävä',
        };
    }

    /**
     * Kuukausikalenterin päivät + kyseisenä päivänä hoidossa
     * olevat eläimet (päivänäkymä ja klikattavuus lisätään
     * omana vaiheenaan myöhemmin).
     */
    private function buildCalendarDays(Carbon $monthStart)
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        $participants = BookingParticipant::with('pet')
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->get();

        $days = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayParticipants = $participants->filter(
                fn ($p) => $p->start_date->lte($date) && $p->end_date->gte($date)
            )->values();

            $days[] = [
                'date' => $date->copy(),
                'count' => $dayParticipants->count(),
                'participants' => $dayParticipants,
            ];
        }

        return $days;
    }
}