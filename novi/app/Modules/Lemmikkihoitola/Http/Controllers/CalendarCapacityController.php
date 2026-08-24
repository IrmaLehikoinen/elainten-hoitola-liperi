<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DateCapacityOverride;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarCapacityController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:date'],
            'species' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $start = Carbon::parse($validated['date']);
        $end = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : $start->copy();
        $species = !empty($validated['species']) ? mb_strtolower(trim($validated['species'])) : null;

                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            DateCapacityOverride::updateOrCreate(
                [
                    'date' => $date->toDateString(),
                    'resource_type' => $species,
                ],
                [
                    'capacity' => $validated['capacity'],
                    'note' => $validated['note'] ?? null,
                ]
            );
        }

        return back()->with('status', 'Kapasiteettimuutos tallennettu.');
    }

    public function destroy(DateCapacityOverride $override)
    {
        $override->delete();

        return back()->with('status', 'Kapasiteettimuutos poistettu.');
    }
}