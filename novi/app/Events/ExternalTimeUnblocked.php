<?php

namespace App\Events;

/**
 * Vastapari ExternalTimeBlocked-tapahtumalle: poistaa aiemmin luodun
 * CalendarBlock-rivin samalla $reason-tunnisteella.
 */
class ExternalTimeUnblocked
{
    public function __construct(
        public int $companyId,
        public string $reason,
    ) {
    }
}