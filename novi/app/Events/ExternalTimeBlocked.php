<?php

namespace App\Events;

use Carbon\Carbon;

/**
 * Toinen moduuli (esim. Lemmikkihoitola) ilmoittaa varanneensa ajan, joka
 * pitää näkyä estettynä myös Ajanvaraus-moduulin (Sydänpolku) kalenterissa.
 * $reason toimii samalla uniikkina tunnisteena: sama $reason päivittää
 * saman CalendarBlock-rivin eikä luo uutta joka kerta.
 */
class ExternalTimeBlocked
{
    public function __construct(
        public int $companyId,
        public Carbon $date,
        public ?string $startTime,
        public ?string $endTime,
        public string $reason,
    ) {
    }
}