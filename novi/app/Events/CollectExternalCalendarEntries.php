<?php

namespace App\Events;

use Illuminate\Support\Carbon;

/**
 * Ajanvaraus-moduulin kalenteri lähettää tämän tapahtuman kysyäkseen
 * "onko tällä aikavälillä jotain muuta varattua muualla" — esim.
 * Kurssit-moduuli kuuntelee tätä ja kertoo kurssien ajat. Ajanvaraus
 * itse ei tiedä mitään Kurssit-moduulista; jos sitä ei ole asennettu,
 * $entries jää vain tyhjäksi eikä mikään hajoa.
 */
class CollectExternalCalendarEntries
{
    /** @var array<int, array{date: string, title: string, color: string}> */
    public array $entries = [];

        public function __construct(
        public Carbon $start,
        public Carbon $end,
        public ?string $source = null,
    ) {
    }
}