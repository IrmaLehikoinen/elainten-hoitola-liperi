<?php

namespace App\Services;

use App\Models\BookingHold;
use App\Models\DateCapacityOverride;
use App\Models\Resource;
use Illuminate\Support\Carbon;

/**
 * POHJA-luokka: geneerinen kapasiteettimoottori. Ei tiedä mitään
 * varauksista, lemmikeistä tai asiakkaista — laskee vain "mahtuuko N
 * kappaletta resurssia tyyppiä X aikavälille". $requirements-taulukoiden
 * avain on aina 'resource_type'.
 *
 * Ainoa tieto jota tämä luokka ei voi itse hakea on "kuinka moni on jo
 * VAHVISTETUSTI varattu" — se riippuu aina toimialamoduulin omasta
 * varauskäsitteestä (esim. lemmikkihoitolassa Booking/BookingParticipant).
 * Sen takia se annetaan konstruktorissa injektoituna funktiona, jonka
 * kutsuva moduuli toimittaa (ks. LemmikkihoitolaServiceProvider::register()).
 */
class AvailabilityService
{
    protected int $searchWindowDays = 365;
    protected int $maxResults = 365;

    /**
     * @param \Closure(string, Carbon): int $confirmedUsageCounter
     *        Palauttaa vahvistettujen (ei peruttujen) varausten määrän
     *        annetulle resurssityypille annettuna päivänä.
     */
        public function __construct(
        protected \Closure $confirmedUsageCounter,
        protected ?int $companyId = null,
    ) {
    }

    /**
     * Palauttaa listan [{iso: 'Y-m-d', display: 'd.m.Y'}] -olioita niistä
     * aloituspäivistä, joista alkaen koko hoitojakso mahtuu resursseihin.
     */
    public function findStartDates(array $requirements, int $durationDays): array
    {
        if (empty($requirements) || $durationDays < 1) {
            return [];
        }

        // Normalisoidaan resurssityyppi pieniksi kirjaimiksi, jotta "Koira" ja "koira" täsmäävät.
        $requirements = collect($requirements)
            ->map(fn ($r) => ['resource_type' => mb_strtolower(trim($r['resource_type'])), 'count' => $r['count']])
            ->all();

        $capacities = $this->resourceTypeCapacities();

        foreach ($requirements as $requirement) {
            $capacity = $capacities[$requirement['resource_type']] ?? 0;

            if ($capacity === 0 || $requirement['count'] > $capacity) {
                return [];
            }
        }

        $dates = [];
        $cursor = Carbon::today();

        for ($i = 0; $i < $this->searchWindowDays; $i++) {
            $startDate = $cursor->copy()->addDays($i);
            $endDate = $startDate->copy()->addDays($durationDays - 1);

            if ($this->fits($requirements, $capacities, $startDate, $endDate)) {
                $dates[] = [
                    'iso' => $startDate->toDateString(),
                    'display' => $startDate->format('d.m.Y'),
                ];

                if (count($dates) >= $this->maxResults) {
                    break;
                }
            }
        }

        return $dates;
    }

    /**
     * Kokonaiskapasiteetti resurssityypeittäin, avaimet pienillä kirjaimilla.
     * Tämä on se yksi paikka (resources-taulu) mistä kapasiteetti luetaan.
     */
        protected function resourceTypeCapacities(): array
    {
        $query = Resource::query()->withoutGlobalScope('company');

        if ($this->companyId) {
            $query->where('company_id', $this->companyId);
        }

        return $query
            ->get(['type', 'capacity'])
            ->groupBy(fn ($resource) => mb_strtolower(trim($resource->type)))
            ->map(fn ($group) => (int) $group->sum('capacity'))
            ->all();
    }

    protected function capacityForDate(string $resourceType, Carbon $date, int $default): int
    {
        $override = DateCapacityOverride::query()
            ->whereDate('date', $date->toDateString())
            ->where(function ($query) use ($resourceType) {
                $query->where('resource_type', $resourceType)->orWhereNull('resource_type');
            })
            ->orderByRaw('resource_type IS NULL')
            ->first();

        return $override ? (int) $override->capacity : $default;
    }

    public function usageForDate(Carbon $date): array
    {
        $capacities = $this->resourceTypeCapacities();
        $result = [];

        foreach ($capacities as $resourceType => $defaultCapacity) {
            $capacity = $this->capacityForDate($resourceType, $date, $defaultCapacity);

            $used = ($this->confirmedUsageCounter)($resourceType, $date);

            $result[$resourceType] = [
                'used' => $used,
                'capacity' => $capacity,
                'default' => $defaultCapacity,
                'overridden' => $capacity !== $defaultCapacity,
            ];
        }

        return $result;
    }

    protected function fits(array $requirements, array $capacities, Carbon $startDate, Carbon $endDate): bool
    {
        foreach ($requirements as $requirement) {
            $resourceType = $requirement['resource_type'];
            $needed = $requirement['count'];
            $defaultCapacity = $capacities[$resourceType] ?? 0;

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $capacity = $this->capacityForDate($resourceType, $date, $defaultCapacity);

                $booked = ($this->confirmedUsageCounter)($resourceType, $date);

                $heldQuery = BookingHold::query()->withoutGlobalScope('company');

                if ($this->companyId) {
                    $heldQuery->where('company_id', $this->companyId);
                }

                $held = (int) $heldQuery
                    ->whereRaw('LOWER(resource_type) = ?', [$resourceType])
                    ->where('expires_at', '>', now())
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->sum('quantity');

                if ($booked + $held + $needed > $capacity) {
                    return false;
                }
            }
        }

            return true;
    }

    /**
     * Julkinen kätevyysmetodi: tarkistaa mahtuuko yksi aloituspäivä + kesto
     * annettuihin vaatimuksiin juuri NYT (käytetään hold() ja store() -vaiheissa
     * uudelleentarkistukseen, jotta kaksi samanaikaista varausta ei mene läpi).
     */
    public function isAvailable(array $requirements, string $startDate, int $durationDays): bool
    {
        $requirements = collect($requirements)
            ->map(fn ($r) => ['resource_type' => mb_strtolower(trim($r['resource_type'])), 'count' => $r['count']])
            ->all();

        $capacities = $this->resourceTypeCapacities();
        $start = Carbon::parse($startDate);
        $end = $start->copy()->addDays($durationDays - 1);

        return $this->fits($requirements, $capacities, $start, $end);
    }
}