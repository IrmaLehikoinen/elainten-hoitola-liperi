<?php

// Pohjan sivuvalikon "koukku" — pohja itse ei tiedä mitään yksittäisistä
// toiminnoista (kalenteri, varaukset, jne). Kukin asennettu moduuli
// ilmoittaa omat valikkokohteensa tähän omassa ServiceProviderissaan,
// ks. esim. App\Modules\Lemmikkihoitola\LemmikkihoitolaServiceProvider.

return [
    'items' => [],
];