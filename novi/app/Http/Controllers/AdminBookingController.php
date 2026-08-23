<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmed;
use App\Mail\BookingPaymentRequired;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminBookingController extends Controller
{
    public function availability(Request $request, \App\Services\AvailabilityService $availability)
    {
        $validated = $request->validate([
            'animals' => ['required', 'array', 'min:1'],
            'animals.*.species' => ['required', 'string'],
            'duration_days' => ['required', 'integer', 'min:1'],
        ]);

                $requirements = collect($validated['animals'])
            ->groupBy('species')
            ->map(fn ($group, $species) => [
                'resource_type' => $species,
                'count' => $group->count(),
            ])
            ->values()
            ->all();

        $dates = $availability->findStartDates($requirements, $validated['duration_days']);

        return response()->json(['dates' => $dates]);
    }

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
            'hold_ids' => ['nullable', 'array'],
            'hold_ids.*' => ['integer'],
        ]);

                 $customer = Customer::findOrFail($validated['customer_id']);

        $arrivalAt = \Carbon\Carbon::parse($validated['arrival_at']);
        $pickupAt = \Carbon\Carbon::parse($validated['pickup_at']);
        $nights = max(1, $arrivalAt->diffInDays($pickupAt) + 1);

        $startDate = $arrivalAt->toDateString();
        $endDate = $pickupAt->toDateString();
        $durationDays = $arrivalAt->copy()->startOfDay()->diffInDays($pickupAt->copy()->startOfDay()) + 1;

        // Vapautetaan oma hold ennen uudelleentarkistusta, jotta se ei laske
        // itseään kahteen kertaan kapasiteetissa (sama periaate kuin julkisessa lomakkeessa).
        if (!empty($validated['hold_ids'])) {
            \App\Models\BookingHold::whereIn('id', $validated['hold_ids'])->delete();
        }

        $requirements = collect($validated['animals'])
            ->map(fn ($animal) => Pet::find($animal['pet_id']))
            ->filter()
            ->groupBy(fn ($pet) => mb_strtolower(trim($pet->species)))
            ->map(fn ($group, $species) => ['resource_type' => $species, 'count' => $group->count()])
            ->values()
            ->all();

        if (!app(\App\Services\AvailabilityService::class)->isAvailable($requirements, $startDate, $durationDays)) {
            return response()->json([
                             'message' => 'Valitettavasti tälle päivälle ei ole enää vapaita paikkoja valitulle lajille. Valitse toinen ajankohta kalenterista.',   
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
                'resource_type' => $pet->species,
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

    /**
     * Merkitsee ennakkomaksun manuaalisesti maksetuksi, kun asiakas on
     * maksanut muuta kautta kuin Stripella (esim. käteinen, tilisiirto).
     */
    public function markDepositPaid(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Vain odottava varaus voidaan merkitä maksetuksi.');
        }

        $booking->update([
            'deposit_paid_at' => now(),
            'status' => 'confirmed',
        ]);

        if ($booking->customer && $booking->customer->email) {
            Mail::to($booking->customer->email)->send(new BookingConfirmed($booking));
        }

        return back()->with('status', 'Ennakkomaksu merkitty maksetuksi ja varaus vahvistettu.');
    } 

    public function acknowledge(Booking $booking)
    {
        if (is_null($booking->acknowledged_at)) {
            $booking->update(['acknowledged_at' => now()]);
        }

        return redirect()->route('admin.customers.show', $booking->customer_id);
    }

    /**
     * Muuttaa YHDEN lemmikin hoitojaksoa kesken varauksen (pidennys/lyhennys),
     * riippumatta muista saman varauksen lemmikeistä. Kapasiteetti tarkistetaan
     * vain niiltä päiviltä jotka eivät kuuluneet lemmikin vanhaan jaksoon.
     */
    public function updateParticipantPeriod(Request $request, \App\Models\BookingParticipant $participant)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $booking = $participant->booking;

        if (!$booking || $booking->status === 'cancelled') {
            return back()->with('error', 'Peruutetun varauksen hoitojaksoa ei voi muuttaa.');
        }

        $newStart = \Carbon\Carbon::parse($validated['start_date'])->startOfDay();
        $newEnd = \Carbon\Carbon::parse($validated['end_date'])->startOfDay();
        $oldStart = $participant->start_date->copy()->startOfDay();
        $oldEnd = $participant->end_date->copy()->startOfDay();

        $newDates = collect();
        for ($d = $newStart->copy(); $d->lte($newEnd); $d->addDay()) {
            if ($d->lt($oldStart) || $d->gt($oldEnd)) {
                $newDates->push($d->copy());
            }
        }

        if ($newDates->isNotEmpty()) {
            $availability = app(\App\Services\AvailabilityService::class);
            $requirements = [['resource_type' => mb_strtolower(trim($participant->resource_type)), 'count' => 1]];

            foreach ($newDates as $date) {
                if (!$availability->isAvailable($requirements, $date->toDateString(), 1)) {
                    return back()->with('error', 'Uusi hoitojakso ei mahdu kapasiteettiin (' . $date->format('d.m.Y') . ').');
                }
            }
        }

        $participant->update([
            'start_date' => $newStart->toDateString(),
            'end_date' => $newEnd->toDateString(),
        ]);

        $booking->load('participants');
        $minStart = $booking->participants->min('start_date');
        $maxEnd = $booking->participants->max('end_date');

        $booking->update([
            'start_date' => $minStart,
            'end_date' => $maxEnd,
            'arrival_at' => $booking->arrival_at->copy()->setDate($minStart->year, $minStart->month, $minStart->day),
            'pickup_at' => $booking->pickup_at->copy()->setDate($maxEnd->year, $maxEnd->month, $maxEnd->day),
        ]);

        return back()->with('status', 'Hoitojakso päivitetty.');
    }
}