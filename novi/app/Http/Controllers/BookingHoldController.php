<?php

namespace App\Http\Controllers;

use App\Models\BookingHold;
use Illuminate\Http\Request;

class BookingHoldController extends Controller
{
    /**
     * Varaa kapasiteetin väliaikaisesti (10 min) kun "Uusi varaus"
     * -ikkuna avataan, jottei kaksi työntekijää voi varata samaa
     * paikkaa päällekkäin kesken lomakkeen täytön.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'animals' => ['required', 'array', 'min:1'],
            'animals.*.species' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1'],
        ]);

        $startDate = $validated['start_date'];
        $endDate = date('Y-m-d', strtotime($startDate.' +'.($validated['duration_days'] - 1).' days'));
        $expiresAt = now()->addMinutes(10);

        $grouped = collect($validated['animals'])
            ->groupBy(fn ($a) => mb_strtolower(trim($a['species'])));

        $holdIds = [];

        foreach ($grouped as $species => $group) {
            $hold = BookingHold::create([
                'species' => $species,
                'quantity' => $group->count(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'expires_at' => $expiresAt,
            ]);

            $holdIds[] = $hold->id;
        }

        return response()->json([
            'hold_ids' => $holdIds,
            'expires_at' => $expiresAt->toIso8601String(),
        ], 201);
    }

    /**
     * Vapauttaa holdit heti kun ikkuna suljetaan tai varaus tallennetaan.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'hold_ids' => ['required', 'array'],
            'hold_ids.*' => ['integer'],
        ]);

        BookingHold::whereIn('id', $validated['hold_ids'])->delete();

        return response()->json(['message' => 'Holdit vapautettu']);
    }
}