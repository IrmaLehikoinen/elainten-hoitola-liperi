<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pet;
use App\Models\Service;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class BookingWizardController extends Controller
{
    public function create()
    {
        $defaultDailyRate = (float) (
            Service::where('pricing_type', 'per_day')->value('price') ?? 0
        );

        return view('bookings.create', [
            'services' => Service::orderBy('name')->get(),
            'defaultDailyRate' => $defaultDailyRate,
        ]);
    }

    public function availability(Request $request, AvailabilityService $availability)
    {
        $validated = $request->validate([
            'animals' => ['required', 'array', 'min:1'],
            'animals.*.species' => ['required', 'string'],
            'duration_days' => ['required', 'integer', 'min:1'],
        ]);

        $requirements = collect($validated['animals'])
            ->groupBy('species')
            ->map(fn ($group, $species) => [
                'species' => $species,
                'count' => $group->count(),
            ])
            ->values()
            ->all();

        $dates = $availability->findStartDates($requirements, $validated['duration_days']);

        return response()->json(['dates' => $dates]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'care_type' => ['required', 'exists:care_types,slug'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'animals' => ['required', 'array', 'min:1'],
            'animals.*.pet_id' => ['required', 'exists:pets,id'],
            'animals.*.daily_rate' => ['nullable', 'numeric', 'min:0'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['exists:services,id'],
            'notes' => ['nullable', 'string'],
        ]);

                    $requirements = collect($validated['animals'])
            ->map(fn ($animal) => Pet::find($animal['pet_id']))
            ->filter()
            ->groupBy(fn ($pet) => mb_strtolower(trim($pet->species)))
            ->map(fn ($group, $species) => ['species' => $species, 'count' => $group->count()])
            ->values()
            ->all();

        $durationDays = \Carbon\Carbon::parse($validated['start_date'])
            ->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;

        if (!app(AvailabilityService::class)->isAvailable($requirements, $validated['start_date'], $durationDays)) {
            return response()->json([
                'message' => 'Valitettavasti kapasiteetti on jo täynnä tälle ajalle. Tarkista kalenteri.',
            ], 422);
        }

        $booking = Booking::create([
            'customer_id' => $validated['customer_id'],
            'care_type' => $validated['care_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'arrival_at' => $validated['start_date'],
            'pickup_at' => $validated['end_date'],
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'priority' => 'normal',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['animals'] as $animal) {
            $pet = Pet::findOrFail($animal['pet_id']);

            $booking->participants()->create([
                'pet_id' => $pet->id,
                'name' => $pet->name,
                'species' => $pet->species,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'daily_rate' => $animal['daily_rate'] ?? 0,
            ]);
        }

        foreach ($validated['service_ids'] ?? [] as $serviceId) {
            $service = Service::find($serviceId);

            if ($service) {
                $booking->bookingServices()->create([
                    'service_id' => $service->id,
                    'price' => $service->price,
                ]);
            }
        }

        return response()->json([
            'message' => 'Varaus tallennettu',
            'booking_id' => $booking->id,
            'customer_id' => $validated['customer_id'],
        ], 201);
    }
}