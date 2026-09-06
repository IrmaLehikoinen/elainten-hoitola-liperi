<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Events\CompanyDateClosed;
use App\Events\CompanyDateReopened;
use App\Http\Controllers\Controller;
use App\Models\DateCapacityOverride;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

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
            $override = DateCapacityOverride::updateOrCreate(
                [
                    'date' => $date->toDateString(),
                    'resource_type' => $species,
                ],
                [
                    'capacity' => $validated['capacity'],
                    'note' => $validated['note'] ?? null,
                ]
            );

            // Koko päivän sulku (ei tiettyä eläinlajia, kapasiteetti 0) —
            // ilmoitetaan muille moduuleille, jotta esim. Ajanvaraus voi
            // sulkea saman päivän omasta kalenteristaan.
            if ($species === null && (int) $validated['capacity'] === 0) {
                Event::dispatch(new CompanyDateClosed($override->company_id, $date->copy(), $validated['note'] ?? null));
            }
        }

        return back()->with('status', 'Kapasiteettimuutos tallennettu.');
    }

    public function destroy(DateCapacityOverride $override)
    {
        $wasWholeDayClosure = $override->resource_type === null && (int) $override->capacity === 0;
        $companyId = $override->company_id;
        $date = $override->date->copy();

        $override->delete();

        if ($wasWholeDayClosure) {
            Event::dispatch(new CompanyDateReopened($companyId, $date));
        }

        return back()->with('status', 'Kapasiteettimuutos poistettu.');
    }
}