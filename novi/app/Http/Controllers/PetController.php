<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * Uuden eläimen pikaluonti varausvelhon vaiheesta 5, kun
     * asiakkaalla ei vielä ole sopivaa eläinkorttia.
     */
        public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'sex' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'microchip_number' => ['nullable', 'string', 'max:255'],
            'vaccinations' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'feeding_instructions' => ['nullable', 'string'],
            'behaviour_notes' => ['nullable', 'string'],
            'veterinarian_name' => ['nullable', 'string', 'max:255'],
            'veterinarian_phone' => ['nullable', 'string', 'max:50'],
            'emergency_notes' => ['nullable', 'string'],
        ]);

        $validated['name'] = $validated['name'] ?? '';

        $pet = Pet::create($validated);

        return response()->json($pet, 201);
    }

    public function show(Pet $pet)
    {
        $pet->load([
            'customer',
            'bookingParticipants.booking',
            'reminders' => fn ($query) => $query->orderByDesc('due_at'),
        ]);

        return view('pets.show', [
            'pet' => $pet,
            'reminderTypes' => \App\Models\ReminderType::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Päivittää eläinkortin kaikki tiedot: perustiedot,
     * terveys-/hoitotiedot, ja kaksi muistiinpanokenttää.
     * general_notes = asiakkaan tiedot (asiakas voisi muokata jatkossa).
     * internal_notes = hoitolan sisäiset, asiakas ei näe koskaan.
     */
    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'sex' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'microchip_number' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'feeding_instructions' => ['nullable', 'string'],
            'behaviour_notes' => ['nullable', 'string'],
            'veterinarian_name' => ['nullable', 'string', 'max:255'],
            'veterinarian_phone' => ['nullable', 'string', 'max:50'],
            'emergency_notes' => ['nullable', 'string'],
            'general_notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $pet->update($validated);

      return redirect()
            ->route('admin.pets.show', [
                'pet' => $pet->id,
                'fromBooking' => $request->boolean('from_booking') ? 1 : null,
            ])
            ->with('status', 'Eläinkortti tallennettu.');
    }

    /**
     * Poistaa lemmikkikortin kokonaan (esim. lemmikin kuoltua).
     */
    public function destroy(Pet $pet)
    {
        $customer = $pet->customer;

        $pet->delete();

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('status', 'Lemmikkikortti poistettu.');
    }
}