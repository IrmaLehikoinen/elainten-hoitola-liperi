<?php

use App\Providers\AppServiceProvider;
use App\Modules\Lemmikkihoitola\LemmikkihoitolaServiceProvider;
use App\Modules\Kurssit\KurssitServiceProvider;

return [
    AppServiceProvider::class,
    LemmikkihoitolaServiceProvider::class,
    KurssitServiceProvider::class,
];