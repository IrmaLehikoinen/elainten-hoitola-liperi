<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Mail\BookingConfirmed;
use Illuminate\Support\Facades\Mail;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response('Virheellinen allekirjoitus', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $bookingId = $session->metadata->booking_id ?? null;

            if ($bookingId) {
                $booking = Booking::find($bookingId);

                if ($booking) {
                    $booking->deposit_paid_at = now();
                    $booking->status = 'confirmed';
                    $booking->save();
                 Mail::to($booking->customer->email)->send(new BookingConfirmed($booking));   
                }
            }
        }

        return response('OK', 200);
    }
}