<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentCategory;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function index()
    {
        return view('ajanvaraus::treatments.index', [
                        'treatments' => Treatment::orderBy('order')->orderBy('name')->get(),
            'categories' => TreatmentCategory::orderBy('order')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('ajanvaraus::treatments.form', [
            'treatment' => new Treatment(['capacity' => 1, 'is_active' => true, 'color' => '#7CAB33']),
            'categories' => TreatmentCategory::orderBy('order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['price'] = $validated['price'] ?? 0;

        $treatment = Treatment::create($validated);   

        return redirect()->route('ajanvaraus.treatments.edit', $treatment)->with('status', 'Hoito tallennettu.');
    }

    public function edit(Treatment $treatment)
    {
        return view('ajanvaraus::treatments.form', [
            'treatment' => $treatment->load(['availabilityRules', 'specialOpenings']),
            'categories' => TreatmentCategory::orderBy('order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Treatment $treatment)
    {
            $validated = $this->validated($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['price'] = $validated['price'] ?? 0;

        $treatment->update($validated);   

        return redirect()->route('ajanvaraus.treatments.edit', $treatment)->with('status', 'Hoito päivitetty.');
    }

        public function destroy(Treatment $treatment)
    {
        $treatment->delete();

        return redirect()->route('ajanvaraus.treatments.index')->with('status', 'Hoito poistettu.');
    }

    public function updateOrganizing(Request $request, Treatment $treatment)
    {
            $validated = $request->validate([
            'order' => ['required', 'integer', 'min:0'],
        ]);

        $treatment->update($validated);

        return back()->with('status', 'Hoito päivitetty.');
    }

        private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
            'treatment_category_id' => ['nullable', 'exists:treatment_categories,id'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:600'],
            'capacity' => ['required', 'integer', 'min:1'],
            'min_participants' => ['nullable', 'integer', 'min:0'],
            'warning_days_before' => ['nullable', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
        ], [
                        'short_description.max' => 'Lyhyt kuvaus voi olla enintään 1000 merkkiä pitkä.',
        ]);
    }
}