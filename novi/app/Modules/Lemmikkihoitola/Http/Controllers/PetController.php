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
            'bookingArrival' => request('arrivalDate') && request('arrivalTime') ? request('arrivalDate').' '.request('arrivalTime') : null,
            'bookingPickup' => request('pickupDate') && request('pickupTime') ? request('pickupDate').' '.request('pickupTime') : null,
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
            'name' => ['nullable', 'string', 'max:255'],
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

        $validated['name'] = $validated['name'] ?? '';

        $pet->update($validated);

        if ($request->filled('due_date')) {
            $allowedTypes = \App\Modules\Lemmikkihoitola\Models\ReminderType::pluck('slug')->all();

            $reminderData = $request->validate([
                'type' => ['required', 'string', 'in:' . implode(',', $allowedTypes)],
                'title' => ['nullable', 'string', 'max:255'],
                'due_date' => ['required', 'date'],
                'due_time' => ['nullable', 'date_format:H:i'],
            ]);

            $dueAtCombined = $reminderData['due_date'].' '.($reminderData['due_time'] ?? '12:00');

            $reminder = \App\Modules\Lemmikkihoitola\Models\Reminder::create([
                'pet_id' => $pet->id,
                'type' => $reminderData['type'],
                'title' => $reminderData['title'] ?? null,
                'due_at' => $dueAtCombined,
                'created_by' => $request->user()->id,
            ]);

            $reminderType = \App\Modules\Lemmikkihoitola\Models\ReminderType::where('slug', $reminderData['type'])->first();

            if ($reminderType && $reminderType->show_in_ajanvaraus_calendar) {
                $dueAt = \Carbon\Carbon::parse($dueAtCombined);

                \Illuminate\Support\Facades\Event::dispatch(new \App\Events\ExternalTimeBlocked(
                    \App\Models\Company::where('industry', 'kurssit')->value('id'),
                    $dueAt->copy(),
                    $dueAt->format('H:i:s'),
                    $dueAt->copy()->addMinutes(30)->format('H:i:s'),
                    'Lemmikkihoitola: muistutus (#'.$reminder->id.')',
                    route('admin.pets.show', $pet->id, false)
                ));
            }
        }

        return redirect()
            ->route('admin.pets.show', array_filter([
                'pet' => $pet->id,
                'fromBooking' => $request->boolean('from_booking') ? 1 : null,
                'arrivalDate' => $request->input('arrivalDate'),
                'arrivalTime' => $request->input('arrivalTime'),
                'pickupDate' => $request->input('pickupDate'),
                'pickupTime' => $request->input('pickupTime'),
            ]))
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