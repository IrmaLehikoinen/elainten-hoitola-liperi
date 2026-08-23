<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Lemmikkihoitola-moduulin näkymät löytyvät tästä kansiosta, pohjan
        // näkymien (resources/views) lisäksi. Laravel etsii molemmista.
        View::addLocation(resource_path('views/modules/lemmikkihoitola'));
    }
}
