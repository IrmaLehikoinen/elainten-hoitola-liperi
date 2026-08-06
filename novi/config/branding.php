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

    'source' => env('NOVI_BRAND_SOURCE', 'website'),

'sources' => [

    'website' => base_path('../project/brand.php'),

    'wordpress' => base_path('project/brand.php'),

],

];