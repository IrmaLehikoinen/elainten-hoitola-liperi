<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));
        $customer = null;
        $visits = collect();

        if ($search !== '') {
            $digitsOnly = preg_replace('/[\s\-]+/', '', $search);

            $customer = Customer::where(function ($builder) use ($search, $digitsOnly) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if ($digitsOnly !== '') {
                        $builder->orWhereRaw(
                            "REPLACE(REPLACE(phone, ' ', ''), '-', '') LIKE ?",
                            ["%{$digitsOnly}%"]
                        );
                    }
                })
                ->first();
        }

                    if ($customer) {
            $visits = Booking::with(['participants', 'bookingServices.service', 'invoice'])
                ->where('customer_id', $customer->id)
                ->whereNotNull('end_date')
                ->where('end_date', '<', today())
                ->orderByDesc('arrival_at')
                ->get()
                ->map(function ($booking) {
                    $booking->nights = $booking->arrival_at && $booking->pickup_at
                        ? max(1, $booking->arrival_at->diffInDays($booking->pickup_at))
                        : null;

                    $booking->servicesTotal = $booking->bookingServices->sum('price');

                    return $booking;
                });
        }

        return view('reports.index', [
            'search' => $search,
            'customer' => $customer,
            'visits' => $visits,
        ]);
    }
}