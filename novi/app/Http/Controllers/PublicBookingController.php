<?php

namespace App\Http\Controllers;

use App\Models\CareType;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    public function start()
    {
             return view('public.booking.step1', [
            'careTypes' => CareType::orderBy('sort_order')->get(),
        ]);   
    }

    public function availability(Request $request, AvailabilityService $availability)
    {
        $validated = $request->validate([
            'animals' => ['required', 'array', 'min:1'],
            'animals.*.species' => ['required', 'string'],
            'care_type' => ['required', 'exists:care_types,slug'],
            'duration_amount' => ['required', 'integer', 'min:1'],
            'duration_unit' => ['required', 'in:days,weeks'],
        ]);

        $durationDays = $validated['duration_unit'] === 'weeks'
            ? $validated['duration_amount'] * 7
            : $validated['duration_amount'];

        session([
            'public_booking.animals' => $validated['animals'],
            'public_booking.care_type' => $validated['care_type'],
            'public_booking.duration_days' => $durationDays,
        ]);

        $requirements = collect($validated['animals'])
            ->groupBy(fn ($a) => mb_strtolower(trim($a['species'])))
            ->map(fn ($group, $species) => ['species' => $species, 'count' => $group->count()])
            ->values()
            ->all();

        $dates = $availability->findStartDates($requirements, $durationDays);

                return view('public.booking.step2', [
            'dates' => $dates,
        ]);
    }

    public function hold(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
        ]);

        $animals = session('public_booking.animals');
        $durationDays = session('public_booking.duration_days');

        if (!$animals || !$durationDays) {
            return redirect()->route('public.booking.start');
        }

        $startDate = $validated['start_date'];
        $endDate = date('Y-m-d', strtotime($startDate.' +'.($durationDays - 1).' days'));
        $expiresAt = now()->addMinutes(10);

        $grouped = collect($animals)->groupBy(fn ($a) => mb_strtolower(trim($a['species'])));
        $holdIds = [];

        foreach ($grouped as $species => $group) {
            $hold = \App\Models\BookingHold::create([
                'species' => $species,
                'quantity' => $group->count(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'expires_at' => $expiresAt,
            ]);

            $holdIds[] = $hold->id;
        }

        session([
            'public_booking.start_date' => $startDate,
            'public_booking.end_date' => $endDate,
            'public_booking.hold_ids' => $holdIds,
            'public_booking.hold_expires_at' => $expiresAt->toIso8601String(),
        ]);

                return view('public.booking.step3', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'expiresAt' => $expiresAt,
        ]);
    }
}