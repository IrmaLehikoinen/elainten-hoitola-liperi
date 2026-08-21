<?php

namespace App\Http\Controllers;

use App\Models\CareType;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    public function start()
    {
             return view('public.booking.step1', [
            'careTypes' => CareType::orderBy('sort_order')->get(),
        ]);   
    }

    public function availability(Request $request, AvailabilityService $availability)
    {
        $validated = $request->validate([
            'animals' => ['required', 'array', 'min:1'],
            'animals.*.species' => ['required', 'string'],
            'care_type' => ['required', 'exists:care_types,slug'],
            'duration_amount' => ['required', 'integer', 'min:1'],
            'duration_unit' => ['required', 'in:days,weeks'],
        ]);

        $durationDays = $validated['duration_unit'] === 'weeks'
            ? $validated['duration_amount'] * 7
            : $validated['duration_amount'];

        session([
            'public_booking.animals' => $validated['animals'],
            'public_booking.care_type' => $validated['care_type'],
            'public_booking.duration_days' => $durationDays,
        ]);

        $requirements = collect($validated['animals'])
            ->groupBy(fn ($a) => mb_strtolower(trim($a['species'])))
            ->map(fn ($group, $species) => ['species' => $species, 'count' => $group->count()])
            ->values()
            ->all();

        $dates = $availability->findStartDates($requirements, $durationDays);

                return view('public.booking.step2', [
            'dates' => $dates,
        ]);
    }

            public function hold(Request $request, \App\Services\AvailabilityService $availability)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
        ]);

        $animals = session('public_booking.animals');
        $durationDays = session('public_booking.duration_days');

        if (!$animals || !$durationDays) {
            return redirect()->route('public.booking.start');
        }

        $requirements = collect($animals)
            ->groupBy(fn ($a) => mb_strtolower(trim($a['species'])))
            ->map(fn ($group, $species) => ['species' => $species, 'count' => $group->count()])
            ->values()
            ->all();

        if (!$availability->isAvailable($requirements, $validated['start_date'], $durationDays)) {
            return redirect()->route('public.booking.start')
                ->with('booking_error', 'Valitettavasti tämä aika ehdittiin juuri varata. Yritä hakea vapaat ajat uudelleen.');
        }

        $startDate = $validated['start_date'];
        $endDate = date('Y-m-d', strtotime($startDate.' +'.($durationDays - 1).' days'));
        $expiresAt = now()->addMinutes(10);

        $grouped = collect($animals)->groupBy(fn ($a) => mb_strtolower(trim($a['species'])));
        $holdIds = [];

                    $companyId = \App\Models\Company::first()->id;

        foreach ($grouped as $species => $group) {
            $hold = \App\Models\BookingHold::create([
                'company_id' => $companyId,
                'species' => $species,
                'quantity' => $group->count(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'expires_at' => $expiresAt,
            ]);

            $holdIds[] = $hold->id;
        }

        session([
            'public_booking.start_date' => $startDate,
            'public_booking.end_date' => $endDate,
            'public_booking.hold_ids' => $holdIds,
            'public_booking.hold_expires_at' => $expiresAt->toIso8601String(),
        ]);

                return view('public.booking.step3', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'expiresAt' => $expiresAt,
        ]);
    }

    public function identify(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        if (!session('public_booking.hold_ids')) {
            return redirect()->route('public.booking.start');
        }

        session(['public_booking.email' => $validated['email']]);

        $customer = \App\Models\Customer::where('email', $validated['email'])->first();

        if (!$customer) {
            return view('public.booking.step4', [
                'customer' => null,
                'animals' => session('public_booking.animals'),
                'email' => $validated['email'],
            ]);
        }

        $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'public.booking.verify',
            now()->addMinutes(30),
            ['customer' => $customer->id]
        );

        \Illuminate\Support\Facades\Mail::to($customer->email)->send(
            new \App\Mail\BookingMagicLink($customer, $signedUrl)
        );

        return view('public.booking.step3', [
            'startDate' => session('public_booking.start_date'),
            'endDate' => session('public_booking.end_date'),
            'linkSent' => true,
            'email' => $validated['email'],
        ]);
    }

    public function verify(Request $request, \App\Models\Customer $customer)
    {
        if (!session('public_booking.hold_ids')) {
            return redirect()->route('public.booking.start');
        }

        session(['public_booking.customer_id' => $customer->id]);

            return view('public.booking.step4', [
            'customer' => $customer->load('pets'),
            'animals' => session('public_booking.animals'),
            'email' => $customer->email,
        ]);
    }

    public function store(Request $request)
    {
        if (!session('public_booking.hold_ids') || !session('public_booking.animals')) {
            return redirect()->route('public.booking.start');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'pets' => ['required', 'array', 'min:1'],
            'pets.*.species' => ['required', 'string'],
            'pets.*.pet_id' => ['nullable', 'exists:pets,id'],
            'pets.*.name' => ['required', 'string', 'max:255'],
            'pets.*.breed' => ['nullable', 'string', 'max:255'],
            'pets.*.birth_date' => ['nullable', 'date'],
            'pets.*.sex' => ['nullable', 'in:uros,naaras'],
            'pets.*.weight' => ['nullable', 'numeric', 'min:0'],
            'pets.*.microchip_number' => ['nullable', 'string', 'max:255'],
            'pets.*.allergies' => ['nullable', 'string'],
            'pets.*.medications' => ['nullable', 'string'],
            'pets.*.feeding_instructions' => ['nullable', 'string'],
            'pets.*.behaviour_notes' => ['nullable', 'string'],
            'pets.*.veterinarian_name' => ['nullable', 'string', 'max:255'],
            'pets.*.veterinarian_phone' => ['nullable', 'string', 'max:255'],
            'pets.*.emergency_notes' => ['nullable', 'string'],
            'pets.*.general_notes' => ['nullable', 'string'],
            'pets.*.booking_notes' => ['nullable', 'string'],
        ]);

        $email = session('public_booking.email');
        $company = \App\Models\Company::first();

        $customerId = session('public_booking.customer_id');
        if ($customerId) {
            $customer = \App\Models\Customer::findOrFail($customerId);
            $customer->update([
                'name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'],
            ]);
        } else {
            $customer = \App\Models\Customer::create([
                'company_id' => $company->id,
                'name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'],
                'email' => $email,
            ]);
        }

        $startDate = session('public_booking.start_date');
        $endDate = session('public_booking.end_date');
        $careType = session('public_booking.care_type');
        $durationDays = session('public_booking.duration_days');

                // Vapautetaan oma hold ennen uudelleentarkistusta, jotta se ei laske
        // itseään kahteen kertaan kapasiteetissa.
        \App\Models\BookingHold::whereIn('id', session('public_booking.hold_ids', []))->delete();

        $requirements = collect(session('public_booking.animals'))
            ->groupBy(fn ($a) => mb_strtolower(trim($a['species'])))
            ->map(fn ($group, $species) => ['species' => $species, 'count' => $group->count()])
            ->values()
            ->all();

        $availability = app(\App\Services\AvailabilityService::class);

        if (!$availability->isAvailable($requirements, $startDate, $durationDays)) {
            return redirect()->route('public.booking.start')
                ->with('booking_error', 'Valitettavasti tämä aika ehdittiin varata juuri ennen kuin varauksesi vahvistui. Yritä uudelleen.');
        }

        $settings = $company->settings ?? [];
        $dailyRate = $customer->custom_daily_rate !== null
            ? (float) $customer->custom_daily_rate
            : (float) ($settings['base_daily_rate'] ?? 0);

        $animalCount = count($validated['pets']);
        $totalPrice = round($dailyRate * $durationDays * $animalCount, 2);

        $depositPercentage = (int) ($settings['deposit_percentage'] ?? 0);
        $depositAmount = round($totalPrice * $depositPercentage / 100, 2);
        $requiresPayment = $depositAmount > 0;

        $booking = \App\Models\Booking::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'arrival_at' => $startDate.' 08:00:00',
            'pickup_at' => $endDate.' 16:00:00',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'care_type' => $careType,
            'status' => $requiresPayment ? 'pending' : 'confirmed',
            'payment_status' => 'unpaid',
            'priority' => 'normal',
            'confirmation_channel' => 'online',
            'total_price' => $totalPrice,
            'deposit_amount' => $depositAmount,
            'payment_deadline' => $requiresPayment ? now()->addDays(2) : null,
        ]);

        foreach ($validated['pets'] as $petData) {
            $petFields = [
                'name' => $petData['name'],
                'species' => $petData['species'],
                'breed' => $petData['breed'] ?? null,
                'birth_date' => $petData['birth_date'] ?? null,
                'sex' => $petData['sex'] ?? null,
                'weight' => $petData['weight'] ?? null,
                'microchip_number' => $petData['microchip_number'] ?? null,
                'allergies' => $petData['allergies'] ?? null,
                'medications' => $petData['medications'] ?? null,
                'feeding_instructions' => $petData['feeding_instructions'] ?? null,
                'behaviour_notes' => $petData['behaviour_notes'] ?? null,
                'veterinarian_name' => $petData['veterinarian_name'] ?? null,
                'veterinarian_phone' => $petData['veterinarian_phone'] ?? null,
                'emergency_notes' => $petData['emergency_notes'] ?? null,
                'general_notes' => $petData['general_notes'] ?? null,
            ];

                $pet = !empty($petData['pet_id'])
                ? \App\Models\Pet::where('customer_id', $customer->id)->find($petData['pet_id'])
                : null;

            if ($pet) {
                $pet->update($petFields);
            } else {
                $pet = $customer->pets()->create($petFields);
            }

            $booking->participants()->create([
                'pet_id' => $pet->id,
                'name' => $pet->name,
                'species' => $pet->species,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'daily_rate' => $dailyRate,
                'notes' => $petData['booking_notes'] ?? null,
            ]);
        }

                session()->forget('public_booking');

        if ($requiresPayment) {
            return redirect()->route('payment.checkout', $booking);
        }

        return redirect()->route('public.booking.start')->with('status', 'Varaus vahvistettu!');
    }
}    