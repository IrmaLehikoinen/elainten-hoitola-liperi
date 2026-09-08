<?php

namespace App\Modules\Kurssit;

use App\Events\CollectExternalCalendarEntries;
use App\Events\StripeCheckoutCompleted;
use App\Models\Company;
use App\Modules\Kurssit\Mail\CourseRegistrationConfirmed;
use App\Modules\Kurssit\Mail\GiftCardIssued;
use App\Modules\Kurssit\Models\Course;
use App\Modules\Kurssit\Models\CourseRegistration;
use App\Modules\Kurssit\Models\GiftCard;
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

        // Moduulin omat migraatiot — kulkevat moduulin mukana.
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        View::addNamespace('kurssit', resource_path('views/modules/kurssit'));

        // Rekisteröidään komentorivikomento käsin, koska se ei asu
        // app/Console/Commands-kansiossa, jota Laravel lukee automaattisesti.
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Kurssit\Console\Commands\SendCourseReminders::class,
                \App\Modules\Kurssit\Console\Commands\CancelExpiredCourseRegistrations::class,
                \App\Modules\Kurssit\Console\Commands\AnonymizeOldCourseData::class,
            ]);
        }

        Config::set('navigation.items', array_merge(
            Config::get('navigation.items', []),
            array_map(fn ($item) => $item + ['industry' => 'kurssit'], [
                            ['route' => 'kurssit.dashboard', 'active_pattern' => 'kurssit.dashboard', 'label' => 'Etusivu', 'icon' => 'home', 'order' => 10],
                ['route' => 'kurssit.cards.index', 'active_pattern' => 'kurssit.cards.*|kurssit.registrations.*', 'label' => 'Kurssikortit', 'icon' => 'calendar', 'order' => 30],
                ['route' => 'kurssit.courses.index', 'active_pattern' => 'kurssit.courses.*', 'label' => 'Syötä uudet kurssit', 'icon' => 'calendar', 'order' => 40],
                ['route' => 'kurssit.reports.index', 'active_pattern' => 'kurssit.reports.*', 'label' => 'Raportti', 'icon' => 'chart', 'order' => 50],
                ['route' => 'kurssit.invoices.index', 'active_pattern' => 'kurssit.invoices.*', 'label' => 'Laskutus', 'icon' => 'euro', 'order' => 60],
                ['route' => 'kurssit.gift-cards.index', 'active_pattern' => 'kurssit.gift-cards.*', 'label' => 'Lahjakortit', 'icon' => 'gift', 'order' => 70],
                ['route' => 'kurssit.settings.index', 'active_pattern' => 'kurssit.settings.*', 'label' => 'Asetukset', 'icon' => 'gear', 'order' => 80],
                ['route' => 'kurssit.customer-data.index', 'active_pattern' => 'kurssit.customer-data.*', 'label' => 'Asiakastiedot', 'icon' => 'shield', 'order' => 90],    
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
                $registration->applyGiftCardIfNeeded();

                Mail::to($registration->email)->send(new CourseRegistrationConfirmed($registration));
            }
        });

        // Sama webhook-tapahtuma, mutta lahjakortin verkko-ostolle: jos
        // metadatassa on gift_card_purchase, luodaan V-koodillinen kortti
        // ja lähetetään se vastaanottajalle (tai ostajalle jos vastaanottajaa
        // ei annettu).
        Event::listen(StripeCheckoutCompleted::class, function (StripeCheckoutCompleted $event) {
            if (($event->metadata['gift_card_purchase'] ?? null) !== '1') {
                return;
            }

            $companyId = $event->metadata['company_id'] ?? null;
            $company = $companyId ? Company::find($companyId) : null;

            if (! $company) {
                return;
            }

            $validityMonths = (int) ($company->settings['gift_cards_validity_months'] ?? 12);
            $amount = (float) ($event->metadata['amount'] ?? 0);

            $giftCard = GiftCard::create([
                'company_id' => $company->id,
                'code' => GiftCard::generateCode('online'),
                'source' => 'online',
                'initial_amount' => $amount,
                'balance' => $amount,
                'valid_until' => now()->addMonths($validityMonths),
                'purchaser_name' => $event->metadata['purchaser_name'] ?? null,
                'purchaser_email' => $event->metadata['purchaser_email'] ?? null,
            ]);

            $recipientEmail = ($event->metadata['recipient_email'] ?? '') ?: null;
            $recipientName = ($event->metadata['recipient_name'] ?? '') ?: null;
            $message = ($event->metadata['message'] ?? '') ?: null;

            $sendTo = $recipientEmail ?: $giftCard->purchaser_email;

            if ($sendTo) {
                Mail::to($sendTo)->send(new GiftCardIssued($giftCard, $recipientName, $message, $giftCard->purchaser_name));
            }
        });

        // Ajanvaraus-moduulin kalenteri kysyy tällä tapahtumalla "onko tällä
        // aikavälillä jotain muuta varattua" — kerrotaan sille kurssien ajat,
        // jotta yhteinen kalenteri näyttää sekä kurssit että hoidot.
                Event::listen(CollectExternalCalendarEntries::class, function (CollectExternalCalendarEntries $event) {
            if ($event->source === 'kurssit') {
                return;
            }

            Course::whereNotNull('starts_at')
                ->whereBetween('starts_at', [$event->start, $event->end])
                ->get()
                ->each(function ($course) use ($event) {
                    $event->entries[] = [
                        'date' => $course->starts_at->format('Y-m-d'),
                        'title' => $course->name,
                        'color' => $course->color ?? app(\App\Core\Branding\BrandManager::class)->get('primary_color'),                            
                    ];
                });
        });
    }
}