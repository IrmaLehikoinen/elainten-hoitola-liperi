<?php

namespace App\Http\Controllers;

use App\Models\BookingParticipant;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Asiakaslista: hoidossa nyt olevat, huomenna saapuvat, ja hakukenttä
     * kaikkien asiakkaiden löytämiseen.
     */
    public function index(Request $request)
    {
        $today = today();
        $tomorrow = today()->addDay();

        $inCareCustomers = BookingParticipant::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereHas('booking', fn ($query) => $query->where('status', '!=', 'cancelled'))
            ->with('booking.customer')
            ->get()
            ->pluck('booking.customer')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $arrivingTomorrowCustomers = BookingParticipant::query()
            ->whereDate('start_date', $tomorrow)
            ->whereHas('booking', fn ($query) => $query->where('status', '!=', 'cancelled'))
            ->with('booking.customer')
            ->get()
            ->pluck('booking.customer')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $search = trim((string) $request->get('q'));

        $searchResults = collect();

        if ($search !== '') {
            $searchResults = Customer::query()
                ->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->get();
        }

        return view('customers.index', [
            'inCareCustomers' => $inCareCustomers,
            'arrivingTomorrowCustomers' => $arrivingTomorrowCustomers,
            'search' => $search,
            'searchResults' => $searchResults,
        ]);
    }

    /**
     * Uuden asiakkaan pikaluonti varausvelhon vaiheesta 4, kun
     * puhelinnumerolla/sähköpostilla ei löytynyt olemassa olevaa
     * asiakasta.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);
        $customer = Customer::create([
            'name' => $validated['name'] ?? '',
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $customer->setRelation('pets', collect());

        return response()->json($customer, 201);
    }

    public function show(Request $request, Customer $customer)
    {
        $customer->load([
            'pets',
            'bookings.participants',
            'invoices' => fn ($query) => $query->orderByDesc('issued_at'),
        ]);

        $today = today();

        $upcomingBookings = $customer->bookings
            ->filter(fn ($booking) => $booking->end_date && $booking->end_date->gte($today))
            ->sortBy('start_date')
            ->values();

        $pastBookings = $customer->bookings
            ->filter(fn ($booking) => ! $booking->end_date || $booking->end_date->lt($today))
            ->sortByDesc('start_date')
            ->values();

        $requiredSpecies = collect(explode(',', (string) $request->get('species')))
            ->map(fn ($species) => trim($species))
            ->filter()
            ->values();

        $remainingCounts = $customer->pets
            ->groupBy(fn ($pet) => strtolower(trim($pet->species)))
            ->map(fn ($group) => $group->count())
            ->toArray();

        $pendingSpecies = collect();

        foreach ($requiredSpecies as $species) {
            $key = strtolower(trim($species));

            if (($remainingCounts[$key] ?? 0) > 0) {
                $remainingCounts[$key]--;
            } else {
                $pendingSpecies->push($species);
            }
        }

        $activeBooking = $upcomingBookings->first();
        $selectedServicesTotal = $activeBooking
            ? $activeBooking->bookingServices()->sum('price')
            : 0;

        return view('customers.show', [
            'customer' => $customer,
            'upcomingBookings' => $upcomingBookings,
            'pastBookings' => $pastBookings,
            'pendingSpecies' => $pendingSpecies,
            'activeBooking' => $activeBooking,
            'selectedServicesTotal' => $selectedServicesTotal,
        ]);
    }

    /**
     * Päivittää asiakkaan perustiedot asiakaskortilta.
     */
    public function update(Request $request, Customer $customer)
    {
    $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'custom_daily_rate' => ['nullable', 'numeric', 'min:0'],
        ]);    

      $customer->update($validated);

        return redirect()
            ->route('admin.customers.show', [
                'customer' => $customer->id,
                'fromBooking' => $request->boolean('from_booking') ? 1 : null,
                'species' => $request->get('species') ?: null,
            ])
            ->with('status', 'Asiakastiedot tallennettu.');
    }
}