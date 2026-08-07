<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class CustomerController extends Controller
{
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