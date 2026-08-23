<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lemmikkihoitola\Models\Booking;
use App\Modules\Lemmikkihoitola\Models\BookingParticipant;
use App\Modules\Lemmikkihoitola\Models\Customer;
use App\Modules\Lemmikkihoitola\Models\Service;
use Illuminate\Http\Request;

class ServiceSelectionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));
        $customer = null;
        $bookings = collect();
        $selectedBooking = null;
        $services = collect();
        $selectedServiceIds = [];
        $selectedPetName = null;

        $customerId = $request->get('customer_id');

        if ($customerId) {
            $customer = Customer::with('pets')->find($customerId);
        } elseif ($search !== '') {
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

        $participantId = $request->get('participant_id');

        if ($participantId) {
            $selectedPetName = optional(BookingParticipant::find($participantId))->name;
        }

        if ($customer) {
            $today = today();

            $bookings = Booking::where('customer_id', $customer->id)
                ->where(function ($q) use ($today) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
                })
                ->orderBy('arrival_at')
                ->get();

            $bookingIdParam = $request->get('booking_id');

            if ($bookingIdParam) {
                $selectedBooking = $bookings->firstWhere('id', (int) $bookingIdParam)
                    ?? Booking::where('customer_id', $customer->id)->find((int) $bookingIdParam);
            } else {
                $selectedBooking = $bookings->first();
            }

            $services = Service::orderBy('name')->get();

            if ($selectedBooking) {
                $selectedServiceIds = $selectedBooking->bookingServices()->pluck('service_id')->all();
            }
        }

        $today = today();

        $inHousePets = BookingParticipant::with(['booking.customer', 'pet'])
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->sortBy(fn ($p) => optional($p->booking)->pickup_at)
            ->values();

        $upcomingPets = BookingParticipant::with(['booking.customer', 'pet'])
            ->whereDate('start_date', '>', $today)
            ->get()
            ->sortBy(fn ($p) => optional($p->booking)->arrival_at)
            ->values();

        return view('services.index', [
            'search' => $search,
            'customer' => $customer,
            'bookings' => $bookings,
            'selectedBooking' => $selectedBooking,
            'services' => $services,
            'selectedServiceIds' => $selectedServiceIds,
            'selectedPetName' => $selectedPetName,
            'inHousePets' => $inHousePets,
            'upcomingPets' => $upcomingPets,
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['exists:services,id'],
        ]);

        $selectedIds = $validated['service_ids'] ?? [];

        $booking->bookingServices()->whereNotIn('service_id', $selectedIds)->delete();

        $existingIds = $booking->bookingServices()->pluck('service_id')->all();

        foreach ($selectedIds as $serviceId) {
            if (in_array((int) $serviceId, $existingIds, true)) {
                continue;
            }

            $service = Service::find($serviceId);

            if ($service) {
                $booking->bookingServices()->create([
                    'service_id' => $service->id,
                    'price' => $service->price,
                ]);
            }
        }

        return redirect()
            ->route('admin.services.index', ['customer_id' => $booking->customer_id, 'booking_id' => $booking->id])
            ->with('status', 'Palvelut tallennettu.');
    }
}