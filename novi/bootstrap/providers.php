<?php

use App\Providers\AppServiceProvider;
use App\Modules\Lemmikkihoitola\LemmikkihoitolaServiceProvider;
use App\Modules\Ajanvaraus\AjanvarausServiceProvider;

return [
    AppServiceProvider::class,
    LemmikkihoitolaServiceProvider::class,
        AjanvarausServiceProvider::class,
];