<?php

namespace App\Modules\Ajanvaraus;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use App\Events\CheckRecurringConflict;
use App\Events\CollectExternalCalendarEntries;
use App\Events\CompanyDateClosed;
use App\Events\CompanyDateReopened;
use App\Events\SchedulingConflictDetected;
use App\Events\StripeCheckoutCompleted;
use App\Models\Company;
use App\Modules\Ajanvaraus\Models\CalendarBlock;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Ajanvaraus-moduulin liitäntäpiste pohjaan, samalla kaavalla kuin
 * LemmikkihoitolaServiceProvider ja KurssitServiceProvider. Tämä moduuli
 * on täysin oma kokonaisuutensa eikä viittaa suoraan Kurssit- tai
 * Lemmikkihoitola-moduulien koodiin, joten sen voi ottaa käyttöön millä
 * tahansa yrityksellä yksinään, tai rinnakkain jonkin toisen moduulin
 * kanssa (ks. Company::active_modules).
 */
class AjanvarausServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

        public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Ajanvaraus\Console\Commands\SendTreatmentGroupWarnings::class,
            ]);
        }

        View::addNamespace('ajanvaraus', resource_path('views/modules/ajanvaraus'));

                Config::set('navigation.items', array_merge(
            Config::get('navigation.items', []),
            array_map(fn ($item) => $item + ['industry' => 'ajanvaraus'], [
                                ['route' => 'ajanvaraus.dashboard', 'active_pattern' => 'ajanvaraus.dashboard|ajanvaraus.calendar.*', 'label' => 'Kalenteri', 'icon' => 'calendar', 'order' => 20],
                            ['route' => 'ajanvaraus.treatments.index', 'active_pattern' => 'ajanvaraus.treatments.*', 'label' => 'Ajanvaraus', 'icon' => 'calendar', 'order' => 21],    
            ])
        ));

                Config::set('industries.ajanvaraus', [
            'label' => 'Ajanvaraus',
            'home_route' => 'ajanvaraus.dashboard',
        ]);

        Event::listen(StripeCheckoutCompleted::class, function (StripeCheckoutCompleted $event) {
            $appointmentId = $event->metadata['treatment_appointment_id'] ?? null;

            if (! $appointmentId) {
                return;
            }

            $appointment = TreatmentAppointment::find($appointmentId);

                        if ($appointment && $appointment->status === 'pending') {
                $appointment->status = 'confirmed';
                $appointment->payment_method = 'stripe';
                $appointment->paid_at = now();
                $appointment->save();
            }
        });

        // Lemmikkihoitola ilmoitti sulkeneensa päivän — suljetaan sama päivä
        // täältäkin, jos tällä yrityksellä on Ajanvaraus käytössä, ja
        // tarkistetaan osuuko sulku johonkin jo olemassa olevaan varaukseen.
        Event::listen(CompanyDateClosed::class, function (CompanyDateClosed $event) {
            $company = Company::find($event->companyId);

            if (! $company || ! in_array('ajanvaraus', $company->active_modules ?? [], true)) {
                return;
            }

            CalendarBlock::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'date' => $event->date->toDateString(),
                    'reason' => 'Lemmikkihoitola: suljettu',
                ],
                []
            );

            TreatmentAppointment::where('company_id', $company->id)
                ->where('status', '!=', 'cancelled')
                ->whereDate('starts_at', $event->date->toDateString())
                ->with('treatment')
                ->get()
                ->each(function ($appointment) use ($company, $event) {
                    Event::dispatch(new SchedulingConflictDetected(
                        $company->id,
                        'Suljettu päivä osuu varaukseen: '.$event->date->translatedFormat('j.n.Y'),
                        ($appointment->treatment->name ?? 'Hoito').' klo '.$appointment->starts_at->format('H:i').' — muista peruuttaa tai siirtää.'
                    ));
                });
        });

        // Päivä avattiin uudelleen — poistetaan vain se sulku jonka
        // Lemmikkihoitola itse loi tänne, ei käsin tehtyjä sulkuja.
                Event::listen(CompanyDateReopened::class, function (CompanyDateReopened $event) {
            CalendarBlock::where('company_id', $event->companyId)
                ->where('date', $event->date->toDateString())
                ->where('reason', 'Lemmikkihoitola: suljettu')
                ->delete();
        });

        // Kurssit kysyy tällä osuuko uusi kurssin ajankohta jonkin
        // viikoittain toistuvan hoidon (esim. "joka sunnuntai" jooga) päälle.
        Event::listen(CheckRecurringConflict::class, function (CheckRecurringConflict $event) {
            $company = Company::find($event->companyId);

            if (! $company || ! in_array('ajanvaraus', $company->active_modules ?? [], true)) {
                return;
            }

            $weekdayNames = ['Sunnuntai', 'Maanantai', 'Tiistai', 'Keskiviikko', 'Torstai', 'Perjantai', 'Lauantai'];

            $treatments = Treatment::with(['availabilityRules', 'specialOpenings'])
                ->where('company_id', $company->id)
                ->where('is_active', true)
                ->get();

            foreach ($treatments as $treatment) {
                foreach ($treatment->availabilityRules as $rule) {
                    if ((int) $rule->weekday !== $event->start->dayOfWeek) {
                        continue;
                    }

                    $ruleStart = $event->start->copy()->setTimeFromTimeString($rule->start_time);
                    $ruleEnd = $event->start->copy()->setTimeFromTimeString($rule->end_time);

                    if ($ruleStart->lt($event->end) && $ruleEnd->gt($event->start)) {
                        $event->conflicts[] = $treatment->name.' (joka '.mb_strtolower($weekdayNames[$rule->weekday]).' klo '.substr($rule->start_time, 0, 5).'–'.substr($rule->end_time, 0, 5).')';
                    }
                }

                foreach ($treatment->specialOpenings as $opening) {
                    if (! \Illuminate\Support\Carbon::parse($opening->date)->isSameDay($event->start)) {
                        continue;
                    }

                    $openStart = $event->start->copy()->setTimeFromTimeString($opening->start_time);
                    $openEnd = $event->start->copy()->setTimeFromTimeString($opening->end_time);

                                        if ($openStart->lt($event->end) && $openEnd->gt($event->start)) {
                        $event->conflicts[] = $treatment->name.' ('.\Illuminate\Support\Carbon::parse($opening->date)->format('d.m.Y').' klo '.substr($opening->start_time, 0, 5).'–'.substr($opening->end_time, 0, 5).')';
                    }
                }
            }
        });

        // Muut moduulit (esim. Kurssit-etusivun pienoiskalenteri) kysyvät
        // tällä "onko tällä aikavälillä jotain muuta varattua" — kerrotaan
        // niille hoitojen varaukset ja viikoittaiset/yksittäiset avaukset.
        // Ohitetaan jos Ajanvaraus itse kysyi, ettei hoidot näy tupla-
        // kertaan omassa kalenterissaan.
        Event::listen(CollectExternalCalendarEntries::class, function (CollectExternalCalendarEntries $event) {
            if ($event->source === 'ajanvaraus') {
                return;
            }

            $appointments = TreatmentAppointment::with('treatment')
                ->whereBetween('starts_at', [$event->start, $event->end])
                ->where('status', '!=', 'cancelled')
                ->get();

            $bookedKeys = $appointments->map(fn ($a) => $a->treatment_id.'|'.$a->starts_at->format('Y-m-d'))->unique();

            $appointments
                ->groupBy(fn ($a) => $a->treatment_id.'|'.$a->starts_at->format('Y-m-d').'|'.$a->starts_at->format('H:i'))
                ->each(function ($group) use ($event) {
                    $first = $group->first();
                    $title = $first->treatment->name.' '.$first->starts_at->format('H:i');

                    if (($first->treatment->capacity ?? 1) > 1) {
                        $title .= ' ('.$group->count().'/'.$first->treatment->capacity.')';
                    }

                                        $event->entries[] = [
                        'date' => $first->starts_at->format('Y-m-d'),
                        'title' => $title,
                        'color' => $first->treatment->color ?? '#999',
                        'filled' => true,
                    ];
                });

            $treatments = Treatment::with(['availabilityRules', 'specialOpenings'])->where('is_active', true)->get();

            for ($date = $event->start->copy()->startOfDay(); $date->lte($event->end); $date->addDay()) {
                foreach ($treatments as $treatment) {
                    if ($bookedKeys->contains($treatment->id.'|'.$date->format('Y-m-d'))) {
                        continue;
                    }

                                    foreach ($treatment->availabilityRules as $rule) {
                        if ((int) $rule->weekday === $date->dayOfWeek) {
                            $event->entries[] = [
                                'date' => $date->format('Y-m-d'),
                                'title' => $treatment->name.' '.substr($rule->start_time, 0, 5),
                                'color' => $treatment->color ?? '#999',
                                'filled' => false,
                            ];
                            break;
                        }
                    }

                    foreach ($treatment->specialOpenings as $opening) {
                        if (\Illuminate\Support\Carbon::parse($opening->date)->isSameDay($date)) {
                            $event->entries[] = [
                                'date' => $date->format('Y-m-d'),
                                'title' => $treatment->name.' '.substr($opening->start_time, 0, 5),
                                'color' => $treatment->color ?? '#999',
                                'filled' => false,
                            ];
                            break;
                        }
                    }   
                }
            }
        });
    }
}