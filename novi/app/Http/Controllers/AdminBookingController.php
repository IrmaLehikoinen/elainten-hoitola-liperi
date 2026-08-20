<?php

namespace App\Http\Controllers;

use App\Mail\BookingPaymentRequired;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminBookingController extends Controller
{
    public function searchCustomer(Request $request)
    {
    $query = trim((string) $request->get('q'));

        if ($query === '') {
            return response()->json(['found' => false], 404);
        }

        $digitsOnly = preg_replace('/[\s\-]+/', '', $query);

        $customer = Customer::with('pets')
            ->where(function ($builder) use ($query, $digitsOnly) {
                $builder->where('email', $query);

                if ($digitsOnly !== '') {
                    $builder->orWhereRaw(
                        "REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?",
                        [$digitsOnly]
                    );
                }
            })
            ->first();

        if (!$customer) {
            return response()->json(['found' => false], 404);
        }

        return response()->json($customer);
    }

        public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));

        $bookings = Booking::with(['customer', 'participants'])
            ->when($search !== '', function ($query) use ($search) {
                $digitsOnly = preg_replace('/[\s\-]+/', '', $search);

                $query->whereHas('customer', function ($customerQuery) use ($search, $digitsOnly) {
                    $customerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if ($digitsOnly !== '') {
                        $customerQuery->orWhereRaw(
                            "REPLACE(REPLACE(phone, ' ', ''), '-', '') LIKE ?",
                            ["%{$digitsOnly}%"]
                        );
                    }
                });
            })
            ->get();

        $today = today();

        $nonPastBookings = $bookings->filter(fn ($b) => ! $b->end_date || $b->end_date->gte($today));

        $awaitingPayment = $nonPastBookings
            ->filter(fn ($b) => $b->status === 'pending')
            ->sortBy('arrival_at')
            ->values();

        $activeBookings = $nonPastBookings
            ->filter(fn ($b) => $b->status === 'confirmed' && $b->start_date && $b->start_date->lte($today))
            ->sortBy('pickup_at')
            ->values();

        $upcomingBookings = $nonPastBookings
            ->filter(fn ($b) => $b->status === 'confirmed' && (! $b->start_date || $b->start_date->gt($today)))
            ->sortBy('arrival_at')
            ->values();

        $pastBookings = $bookings
            ->filter(fn ($b) => $b->end_date && $b->end_date->lt($today))
            ->sortByDesc('arrival_at')
            ->values();

        return view('bookings.index', [
            'activeBookings' => $activeBookings,
            'awaitingPayment' => $awaitingPayment,
            'upcomingBookings' => $upcomingBookings,
            'pastBookings' => $pastBookings,
            'search' => $search,
            'careTypeLabels' => \App\Models\CareType::pluck('label', 'slug'),
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['customer', 'participants.pet', 'bookingServices.service', 'invoice']);

        return view('bookings.show', [
            'booking' => $booking,
            'careTypeLabels' => \App\Models\CareType::pluck('label', 'slug'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'animals' => ['required', 'array', 'min:1'],
            'animals.*.pet_id' => ['required', 'exists:pets,id'],
            'arrival_at' => ['required', 'date'],
            'pickup_at' => ['required', 'date', 'after_or_equal:arrival_at'],
            'care_type' => ['required', 'exists:care_types,slug'],
            'notes' => ['nullable', 'string'],
        ]);

                 $customer = Customer::findOrFail($validated['customer_id']);

        $arrivalAt = \Carbon\Carbon::parse($validated['arrival_at']);
        $pickupAt = \Carbon\Carbon::parse($validated['pickup_at']);
        $nights = max(1, $arrivalAt->diffInDays($pickupAt));

        $startDate = $arrivalAt->toDateString();
        $endDate = $pickupAt->toDateString();
        $durationDays = $arrivalAt->copy()->startOfDay()->diffInDays($pickupAt->copy()->startOfDay()) + 1;

        $requirements = collect($validated['animals'])
            ->map(fn ($animal) => Pet::find($animal['pet_id']))
            ->filter()
            ->groupBy(fn ($pet) => mb_strtolower(trim($pet->species)))
            ->map(fn ($group, $species) => ['species' => $species, 'count' => $group->count()])
            ->values()
            ->all();

        if (!app(\App\Services\AvailabilityService::class)->isAvailable($requirements, $startDate, $durationDays)) {
            return response()->json([
                'message' => 'Valitettavasti kapasiteetti on jo täynnä tälle ajalle. Tarkista kalenteri.',
            ], 422);
        }

        $company = $request->user()->company; 
        $settings = $company->settings ?? [];
        $baseDailyRate = (float) ($settings['base_daily_rate'] ?? 0);
        $dailyRate = $customer->custom_daily_rate !== null
            ? (float) $customer->custom_daily_rate
            : $baseDailyRate;

        $animalCount = count($validated['animals']);
        $totalPrice = round($dailyRate * $nights * $animalCount, 2);

        $depositPercentage = (int) ($settings['deposit_percentage'] ?? 0);
        $depositAmount = round($totalPrice * $depositPercentage / 100, 2);
        $requiresPayment = $depositAmount > 0;

        $twoDaysOut = now()->addDays(2);
        $paymentDeadline = $twoDaysOut->lt($arrivalAt) ? $twoDaysOut : $arrivalAt;

        $booking = Booking::create([
            'company_id' => $request->user()->company_id,
            'customer_id' => $validated['customer_id'],
            'arrival_at' => $validated['arrival_at'],
            'pickup_at' => $validated['pickup_at'],
            'start_date' => date('Y-m-d', strtotime($validated['arrival_at'])),
            'end_date' => date('Y-m-d', strtotime($validated['pickup_at'])),
            'care_type' => $validated['care_type'],
            'status' => $requiresPayment ? 'pending' : 'confirmed',
            'payment_status' => 'unpaid',
            'priority' => 'normal',
            'notes' => $validated['notes'] ?? null,
            'total_price' => $totalPrice,
            'deposit_amount' => $depositAmount,
            'payment_deadline' => $requiresPayment ? $paymentDeadline : null,
        ]);

        foreach ($validated['animals'] as $animal) {
            $pet = Pet::findOrFail($animal['pet_id']);

            $booking->participants()->create([
                'pet_id' => $pet->id,
                'name' => $pet->name,
                'species' => $pet->species,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
                'daily_rate' => $dailyRate,
            ]);
        }

        $paymentUrl = $requiresPayment ? route('payment.checkout', $booking) : null;

        if ($requiresPayment && $customer->email) {
            Mail::to($customer->email)->send(new BookingPaymentRequired($booking, $paymentUrl));
        }

        return response()->json([
            'message' => 'Varaus tallennettu',
            'booking' => $booking,
            'payment_url' => $paymentUrl,
            'email_sent' => $requiresPayment && $customer->email ? true : false,
        ], 201);
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);

        return back()->with('status', 'Varaus peruttu.');
    }
}