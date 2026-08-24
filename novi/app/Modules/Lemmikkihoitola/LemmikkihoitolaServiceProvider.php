<?php

namespace App\Modules\Lemmikkihoitola;

use App\Events\StripeCheckoutCompleted;
use App\Modules\Lemmikkihoitola\Mail\BookingConfirmed;
use App\Modules\Lemmikkihoitola\Models\Booking;
use App\Modules\Lemmikkihoitola\Models\BookingParticipant;
use App\Services\AvailabilityService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * MALLIESIMERKKI TULEVALLE TOIMIALAMODUULILLE (esim. parturi):
 * Tämä on se YKSI tiedosto jonka kautta moduuli liittyy pohjaan — pohja ei
 * tunne moduulia mistään muualta. Kopioi tämän tiedoston RAKENNE (ei
 * sisältöä) uudelle moduulille: register() kytkee pohjan geneeriset
 * palvelut (AvailabilityService, Stripe) moduulin omaan dataan; boot()
 * rekisteröi näkymäpolun, reitit, valikon, konfiguraatiot ja komennot.
 */
class LemmikkihoitolaServiceProvider extends ServiceProvider
{
    /**
     * Kytkee pohjan geneerisen kapasiteettimoottorin (AvailabilityService)
     * lemmikkihoitolan omaan "kuinka moni on jo vahvistetusti varattu"
     * -kyselyyn. Pohja ei tiedä mitään Bookingista tai BookingParticipantista
     * — se saa vain tämän yhden funktion, joka palauttaa luvun.
     */
    public function register(): void
    {
        $this->app->bind(AvailabilityService::class, function () {
            return new AvailabilityService(function (string $resourceType, Carbon $date): int {
                return BookingParticipant::query()
                    ->whereRaw('LOWER(resource_type) = ?', [$resourceType])
                    ->whereHas('booking', function ($query) {
                        $query->where('status', '!=', 'cancelled');
                    })
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->count();
            });
        });
    }

    /**
     * Lemmikkihoitola-moduulin omat sivuvalikon kohteet, näkymäpolku ja
     * ajastettu komento. Pohja ei tunne näitä suoraan.
     */
            public function boot(): void
    {
        // Moduulin omat reitit — pohja ei tunne niitä suoraan.
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Moduulin omat näkymät löytyvät tästä kansiosta pohjan
        // näkymien (resources/views) lisäksi. Pohja ei tunne tätä polkua.
        View::addLocation(resource_path('views/modules/lemmikkihoitola'));

        Config::set('navigation.items', array_merge(
            Config::get('navigation.items', []),
            [
                ['route' => 'dashboard', 'active_pattern' => 'dashboard', 'label' => 'Etusivu', 'icon' => 'home'],
                ['route' => 'calendar.index', 'active_pattern' => 'calendar.*', 'label' => 'Kalenteri', 'icon' => 'calendar'],
                ['route' => 'admin.bookings.index', 'active_pattern' => 'admin.bookings.index', 'label' => 'Varaukset', 'icon' => 'check'],
                ['route' => 'admin.customers.index', 'active_pattern' => 'admin.customers.*', 'label' => 'Asiakkaat', 'icon' => 'users'],
                ['route' => 'admin.services.index', 'active_pattern' => 'admin.services.*', 'label' => 'Palvelut', 'icon' => 'shield'],
                ['route' => 'admin.invoices.index', 'active_pattern' => 'admin.invoices.*', 'label' => 'Laskutus', 'icon' => 'euro'],
                ['route' => 'admin.reports.index', 'active_pattern' => 'admin.reports.*', 'label' => 'Raportit', 'icon' => 'chart'],
                ['route' => 'admin.settings.index', 'active_pattern' => 'admin.settings.*', 'label' => 'Asetukset', 'icon' => 'gear'],
            ]
        ));

        Config::set('industries.lemmikkihoitola', [
            'label' => 'Lemmikkihoitola',
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/public_booking_fields.php',
            'public_booking_fields'
        );

        // Ajastettu komento (bookings:cancel-expired) pitää rekisteröidä
        // käsin, koska se ei asu app/Console/Commands-kansiossa, jota
        // Laravel etsii automaattisesti.
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Lemmikkihoitola\Console\Commands\CancelExpiredBookings::class,
            ]);
        }

        // Pohjan Stripe-webhook ilmoittaa vain "maksu onnistui" -tapahtumasta,
        // ei tiedä mitään varauksista. Tämä on se paikka jossa moduuli
        // kertoo mitä sillä tiedolla tehdään.
        Event::listen(StripeCheckoutCompleted::class, function (StripeCheckoutCompleted $event) {
            $bookingId = $event->metadata['booking_id'] ?? null;

            if (! $bookingId) {
                return;
            }

            $booking = Booking::find($bookingId);

            if ($booking && $booking->status === 'pending') {
                $booking->deposit_paid_at = now();
                $booking->status = 'confirmed';
                $booking->save();
                Mail::to($booking->customer->email)->send(new BookingConfirmed($booking));
            }
        });
    }
}