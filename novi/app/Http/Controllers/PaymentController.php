<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function checkout(Booking $booking)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $customer = $stripe->customers->create([
            'email' => $booking->customer->email,
            'name' => $booking->customer->name,
        ]);

        // Stripen maksusivu saa olla voimassa korkeintaan 24h ja vähintään 30min
        // (Stripen oma rajoitus). Käytetään varauksen omaa maksuaikaa, mutta
        // pysytään näiden rajojen sisällä.
        $expiresAt = now()->addHours(24);

        if ($booking->payment_deadline && $booking->payment_deadline->lt($expiresAt)) {
            $expiresAt = $booking->payment_deadline;
        }

        if ($expiresAt->lt(now()->addMinutes(30))) {
            $expiresAt = now()->addMinutes(30);
        }

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Varausmaksu - varaus #' . $booking->id,
                    ],
                    'unit_amount' => intval($booking->deposit_amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer' => $customer->id,
            'metadata' => [
                'booking_id' => $booking->id,
            ],
            'expires_at' => $expiresAt->timestamp,
            'success_url' => url('/maksu/onnistui') . '?booking=' . $booking->id,
            'cancel_url' => url('/maksu/peruttu'),
        ]);

        return redirect($session->url);
    }
}