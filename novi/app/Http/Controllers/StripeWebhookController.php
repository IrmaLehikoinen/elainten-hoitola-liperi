<?php

namespace App\Http\Controllers;

use App\Events\StripeCheckoutCompleted;
use Illuminate\Http\Request;
use Stripe\Webhook;

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

            event(new StripeCheckoutCompleted(
                (array) ($session->metadata ?? [])
            ));
        }

        return response('OK', 200);
    }
}