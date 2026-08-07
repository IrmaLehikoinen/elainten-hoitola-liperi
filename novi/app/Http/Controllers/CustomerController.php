<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
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
            'name' => $validated['name'] ?: 'Uusi asiakas',
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $customer->setRelation('pets', collect());

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
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

        return view('customers.show', [
            'customer' => $customer,
            'upcomingBookings' => $upcomingBookings,
            'pastBookings' => $pastBookings,
        ]);
    }
}