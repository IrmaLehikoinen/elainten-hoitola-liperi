<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function show(Pet $pet)
    {
        $pet->load([
            'customer',
            'bookingParticipants.booking',
            'reminders' => fn ($query) => $query->orderByDesc('due_at'),
        ]);

        return view('pets.show', [
            'pet' => $pet,
        ]);
    }

    /**
     * Päivittää eläinkortin kaksi muistiinpanokenttää.
     * general_notes = asiakkaan tiedot (asiakas voisi muokata jatkossa).
     * internal_notes = hoitolan sisäiset, asiakas ei näe koskaan.
     */
    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'general_notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $pet->update($validated);

        return redirect()
            ->route('admin.pets.show', $pet)
            ->with('status', 'Muistiinpanot tallennettu.');
    }
}