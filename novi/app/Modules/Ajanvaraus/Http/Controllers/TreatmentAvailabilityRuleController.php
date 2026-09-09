<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentAvailabilityRule;
use Illuminate\Http\Request;

class TreatmentAvailabilityRuleController extends Controller
{
    public function store(Request $request, Treatment $treatment)
    {
        $validated = $request->validate([
            'weekday' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reminder_note' => ['nullable', 'string', 'max:2000'],
            'reminder_date' => ['nullable', 'date'],
        ]);

        $conflict = $this->findConflictingTreatment($treatment, (int) $validated['weekday'], $validated['start_time'], $validated['end_time']);

        if ($conflict) {
            $editUrl = route('ajanvaraus.treatments.edit', $conflict->id);
            return back()->withErrors(['start_time' => 'Tämä aika menee päällekkäin hoidon "'.$conflict->name.'" kanssa samana viikonpäivänä. <a href="'.$editUrl.'" class="underline font-semibold">Muokkaa sitä aikaa →</a>']);
        }

        $treatment->availabilityRules()->create($validated);

        return back()->with('status', 'Viikkoaika lisätty.');
    }

    public function destroy(TreatmentAvailabilityRule $rule)
    {
        $rule->delete();

        return back()->with('status', 'Viikkoaika poistettu.');
    }

    /**
     * Palauttaa TOISEN hoidon jos tämä viikonpäivä+aika menee
     * päällekkäin jonkin toisen hoidon viikoittaisen säännön kanssa.
     */
    private function findConflictingTreatment(Treatment $treatment, int $weekday, string $startTime, string $endTime): ?Treatment
    {
        $others = Treatment::where('id', '!=', $treatment->id)->with('availabilityRules')->get();

        foreach ($others as $other) {
            foreach ($other->availabilityRules as $rule) {
                if ((int) $rule->weekday === $weekday && $rule->start_time < $endTime && $rule->end_time > $startTime) {
                    return $other;
                }
            }
        }

        return null;
    }
}