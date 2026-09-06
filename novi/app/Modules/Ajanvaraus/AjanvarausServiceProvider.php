<?php

namespace App\Modules\Ajanvaraus;

use Illuminate\Support\Facades\View;
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

        View::addNamespace('ajanvaraus', resource_path('views/modules/ajanvaraus'));
    }
}