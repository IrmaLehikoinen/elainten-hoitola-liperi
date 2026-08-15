<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Pet;
use Illuminate\Http\Request;

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

        $booking = Booking::create([
            'company_id' => $request->user()->company_id,
            'customer_id' => $validated['customer_id'],
            'arrival_at' => $validated['arrival_at'],
            'pickup_at' => $validated['pickup_at'],
            'start_date' => date('Y-m-d', strtotime($validated['arrival_at'])),
            'end_date' => date('Y-m-d', strtotime($validated['pickup_at'])),
            'care_type' => $validated['care_type'],
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
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
            ]);
        }

        return response()->json([
            'message' => 'Varaus tallennettu',
            'booking' => $booking,
        ], 201);
    }
}