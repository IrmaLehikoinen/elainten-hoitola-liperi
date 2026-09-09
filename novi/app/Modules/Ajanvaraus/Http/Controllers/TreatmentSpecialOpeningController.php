<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentSpecialOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TreatmentSpecialOpeningController extends Controller
{
    public function store(Request $request, Treatment $treatment)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reminder_note' => ['nullable', 'string', 'max:2000'],
            'reminder_date' => ['nullable', 'date'],
        ]);

        $conflict = $this->findConflictingTreatment($treatment, $validated['date'], $validated['start_time'], $validated['end_time']);

        if ($conflict) {
            $editUrl = route('ajanvaraus.treatments.edit', $conflict->id);
            return back()->withErrors(['start_time' => 'Tämä aika menee päällekkäin hoidon "'.$conflict->name.'" kanssa. <a href="'.$editUrl.'" class="underline font-semibold">Muokkaa sitä aikaa →</a>']);
        }

        $treatment->specialOpenings()->create($validated);

        return back()->with('status', 'Yksittäinen avaus lisätty.');
    }

    public function storeForAny(Request $request)
    {
        $validated = $request->validate([
            'treatment_id' => ['required', 'exists:treatments,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reminder_note' => ['nullable', 'string', 'max:2000'],
            'reminder_date' => ['nullable', 'date'],
        ]);

        $treatment = Treatment::findOrFail($validated['treatment_id']);

        $conflict = $this->findConflictingTreatment($treatment, $validated['date'], $validated['start_time'], $validated['end_time']);

        if ($conflict) {
            $editUrl = route('ajanvaraus.treatments.edit', $conflict->id);
            return back()->withErrors(['start_time' => 'Tämä aika menee päällekkäin hoidon "'.$conflict->name.'" kanssa. <a href="'.$editUrl.'" class="underline font-semibold">Muokkaa sitä aikaa →</a>']);
        }

        $treatment->specialOpenings()->create([
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reminder_note' => $validated['reminder_note'] ?? null,
            'reminder_date' => $validated['reminder_date'] ?? null,
        ]);

        return back()->with('status', 'Yksittäinen avaus lisätty.');
    }

    public function destroy(TreatmentSpecialOpening $opening)
    {
        $opening->delete();

        return back()->with('status', 'Yksittäinen avaus poistettu.');
    }

    /**
     * Palauttaa TOISEN hoidon jos tämä päivä+aika menee päällekkäin
     * jonkin toisen hoidon viikoittaisen säännön tai yksittäisen avauksen
     * kanssa — sama yrittäjä ei voi tehdä kahta asiaa yhtä aikaa.
     */
    private function findConflictingTreatment(Treatment $treatment, string $date, string $startTime, string $endTime): ?Treatment
    {
        $weekday = Carbon::parse($date)->dayOfWeek;

        $others = Treatment::where('id', '!=', $treatment->id)
            ->with(['availabilityRules', 'specialOpenings'])
            ->get();

        foreach ($others as $other) {
            foreach ($other->availabilityRules as $rule) {
                if ((int) $rule->weekday === $weekday && $rule->start_time < $endTime && $rule->end_time > $startTime) {
                    return $other;
                }
            }

            foreach ($other->specialOpenings as $opening) {
                if (Carbon::parse($opening->date)->toDateString() === $date && $opening->start_time < $endTime && $opening->end_time > $startTime) {
                    return $other;
                }
            }
        }

        return null;
    }
}