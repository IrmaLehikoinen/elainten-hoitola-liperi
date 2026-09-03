<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lemmikkihoitola\Models\Pet;
use App\Services\ActiveCompanyResolver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PetController extends Controller
{
    /**
     * Uuden eläimen pikaluonti varausvelhon vaiheesta 5, kun
     * asiakkaalla ei vielä ole sopivaa eläinkorttia.
     */
    public function store(Request $request)
    {
        $activeCompanyId = app(ActiveCompanyResolver::class)->current()?->id;

        $validated = $request->validate([
            'customer_id' => ['required', Rule::exists('customers', 'id')->where('company_id', $activeCompanyId)],
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
        $this->authorizePetAccess($pet);

        $pet->load([
            'customer',
            'bookingParticipants.booking',
            'reminders' => fn ($query) => $query->orderByDesc('due_at'),
        ]);

        return view('pets.show', [
            'pet' => $pet,
            'reminderTypes' => \App\Modules\Lemmikkihoitola\Models\ReminderType::orderBy('sort_order')->get(),
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
        $this->authorizePetAccess($pet);

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
            ->with('status', 'Lemmikkikortti tallennettu.');
    }

    /**
     * Poistaa lemmikkikortin kokonaan (esim. lemmikin kuoltua).
     */
    public function destroy(Pet $pet)
    {
        $this->authorizePetAccess($pet);

        $customer = $pet->customer;

        $pet->delete();

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('status', 'Lemmikkikortti poistettu.');
    }

    /**
     * Estää käyttäjää käsittelemästä toisen yrityksen lemmikkikorttia
     * pelkän ID:n arvaamalla (IDOR-suojaus, ks. UUSI_ASIAKAS_OHJE.md).
     */
    private function authorizePetAccess(Pet $pet): void
    {
        $activeCompanyId = app(ActiveCompanyResolver::class)->current()?->id;

        abort_unless(
            optional($pet->customer)->company_id === $activeCompanyId,
            404
        );
    }
}