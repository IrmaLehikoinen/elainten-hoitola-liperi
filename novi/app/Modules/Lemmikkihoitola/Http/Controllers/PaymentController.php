<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lemmikkihoitola\Models\Booking;
use App\Services\StripeCheckoutService;

/**
 * MALLIESIMERKKI: näin moduuli käyttää pohjan geneeristä
 * StripeCheckoutServiceä — kokoaa itse omat tietonsa (Booking-mallista)
 * ja antaa ne pohjalle yleisinä avain-arvo-pareina. Tulevan moduulin
 * omaa maksuvirtaa varten: kopioi tämä kaava, ei tarvitse koskea
 * StripeCheckoutServiceen ollenkaan.
 */
class PaymentController extends Controller
{
    public function checkout(Booking $booking, StripeCheckoutService $checkout)
    {
        if ($booking->status !== 'pending' || $booking->deposit_paid_at) {
            abort(404);
        }

        $url = $checkout->createSessionUrl([
            'email' => $booking->customer->email,
            'name' => $booking->customer->name,
            'amount' => (float) $booking->deposit_amount,
            'description' => 'Varausmaksu - varaus #' . $booking->id,
            'metadata' => ['booking_id' => $booking->id],
            'preferred_deadline' => $booking->payment_deadline,
            'success_url' => url('/maksu/onnistui') . '?booking=' . $booking->id,
            'cancel_url' => url('/maksu/peruttu'),
        ]);

        return redirect($url);
    }
}