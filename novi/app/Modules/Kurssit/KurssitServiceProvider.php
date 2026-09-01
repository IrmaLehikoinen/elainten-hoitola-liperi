<?php

namespace App\Modules\Kurssit;

use App\Events\StripeCheckoutCompleted;
use App\Modules\Kurssit\Mail\CourseRegistrationConfirmed;
use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Kurssit-moduulin liitäntäpiste pohjaan, samalla kaavalla kuin
 * LemmikkihoitolaServiceProvider.
 */
class KurssitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
            $this->loadRoutesFrom(__DIR__.'/routes.php');

        View::addNamespace('kurssit', resource_path('views/modules/kurssit'));

        // Rekisteröidään komentorivikomento käsin, koska se ei asu
        // app/Console/Commands-kansiossa, jota Laravel lukee automaattisesti.
                if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Kurssit\Console\Commands\SendCourseReminders::class,
                \App\Modules\Kurssit\Console\Commands\CancelExpiredCourseRegistrations::class,
            ]);
        }

        Config::set('navigation.items', array_merge(
            Config::get('navigation.items', []),
            array_map(fn ($item) => $item + ['industry' => 'kurssit'], [
                            ['route' => 'kurssit.dashboard', 'active_pattern' => 'kurssit.dashboard', 'label' => 'Etusivu', 'icon' => 'home'],
                ['route' => 'kurssit.cards.index', 'active_pattern' => 'kurssit.cards.*|kurssit.registrations.*', 'label' => 'Kurssikortit', 'icon' => 'calendar'],
                ['route' => 'kurssit.courses.index', 'active_pattern' => 'kurssit.courses.*', 'label' => 'Syötä uudet kurssit', 'icon' => 'calendar'],
                ['route' => 'kurssit.reports.index', 'active_pattern' => 'kurssit.reports.*', 'label' => 'Raportti', 'icon' => 'chart'],
                ['route' => 'kurssit.invoices.index', 'active_pattern' => 'kurssit.invoices.*', 'label' => 'Laskutus', 'icon' => 'euro'],
                ['route' => 'kurssit.settings.index', 'active_pattern' => 'kurssit.settings.*', 'label' => 'Asetukset', 'icon' => 'gear'],
            ])
        ));

        Config::set('industries.kurssit', [
            'label' => 'Kurssit',
            'home_route' => 'kurssit.dashboard',
        ]);

        // Pohjan Stripe-webhook ilmoittaa vain "maksu onnistui" -tapahtumasta,
        // ei tiedä mitään kurssi-ilmoittautumisista. Tämä on se paikka jossa
        // Kurssit-moduuli kertoo mitä sillä tiedolla tehdään — täsmälleen
        // sama kaava kuin LemmikkihoitolaServiceProviderissa.
        Event::listen(StripeCheckoutCompleted::class, function (StripeCheckoutCompleted $event) {
            $registrationId = $event->metadata['course_registration_id'] ?? null;

            if (! $registrationId) {
                return;
            }

            $registration = CourseRegistration::find($registrationId);

            if ($registration && $registration->status === 'pending') {
                $registration->status = 'confirmed';
                $registration->payment_method = 'stripe';
                $registration->paid_at = now();
                $registration->save();

                Mail::to($registration->email)->send(new CourseRegistrationConfirmed($registration));
            }
        });
    }
}