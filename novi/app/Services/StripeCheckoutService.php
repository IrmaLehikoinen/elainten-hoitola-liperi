<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Stripe\StripeClient;

/**
 * POHJA-luokka: geneerinen Stripe-kertamaksun (checkout-sivun) luonti.
 * Ei tiedä mitään varauksista — ottaa vastaan yleiset maksutiedot
 * (summa, kuvaus, sähköposti, metadata) ja palauttaa Stripe-maksusivun
 * osoitteen. Kutsuva moduuli päättää mistä nämä tiedot tulevat.
 */
class StripeCheckoutService
{
    public function createSessionUrl(array $params): string
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $customer = $stripe->customers->create([
            'email' => $params['email'],
            'name' => $params['name'],
        ]);

        $expiresAt = $this->resolveExpiry($params['preferred_deadline'] ?? null);

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $params['currency'] ?? 'eur',
                    'product_data' => [
                        'name' => $params['description'],
                    ],
                    'unit_amount' => intval($params['amount'] * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer' => $customer->id,
            'metadata' => $params['metadata'] ?? [],
            'expires_at' => $expiresAt->timestamp,
            'success_url' => $params['success_url'],
            'cancel_url' => $params['cancel_url'],
        ]);

        return $session->url;
    }

    /**
     * Stripen oma sääntö: maksusivu voi olla voimassa korkeintaan 24h ja
     * vähintään 30min. Käytetään mieluiten kutsujan omaa määräaikaa, mutta
     * pysytään näiden rajojen sisällä.
     */
    protected function resolveExpiry(?Carbon $preferredDeadline): Carbon
    {
        $expiresAt = now()->addHours(24);

        if ($preferredDeadline && $preferredDeadline->lt($expiresAt)) {
            $expiresAt = $preferredDeadline;
        }

        if ($expiresAt->lt(now()->addMinutes(30))) {
            $expiresAt = now()->addMinutes(30);
        }

        return $expiresAt;
    }
}