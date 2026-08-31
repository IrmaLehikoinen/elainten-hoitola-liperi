<?php

namespace App\Modules\Kurssit;

use Illuminate\Support\Facades\Config;
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

        Config::set('navigation.items', array_merge(
            Config::get('navigation.items', []),
            array_map(fn ($item) => $item + ['industry' => 'kurssit'], [
                ['route' => 'kurssit.dashboard', 'active_pattern' => 'kurssit.*', 'label' => 'Etusivu', 'icon' => 'home'],
            ])
        ));

        Config::set('industries.kurssit', [
            'label' => 'Kurssit',
            'home_route' => 'kurssit.dashboard',
        ]);
    }
}