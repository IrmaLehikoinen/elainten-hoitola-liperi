<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function checkout(Booking $booking)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

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
            'success_url' => url('/maksu/onnistui'),
            'cancel_url' => url('/maksu/peruttu'),
        ]);

        return redirect($session->url);
    }
}