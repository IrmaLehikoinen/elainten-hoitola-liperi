<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mistä Novi hakee brändin
    |--------------------------------------------------------------------------
    |
    | shared = yhteinen projektin brand.php (koodatut verkkosivut)
    | local  = Novin oma brand.php (WordPress)
    |
    */

    'source' => env('NOVI_BRAND_SOURCE', 'shared'),

    'sources' => [

        'shared' => base_path('../project/brand.php'),

        'local' => base_path('project/brand.php'),

    ],

];