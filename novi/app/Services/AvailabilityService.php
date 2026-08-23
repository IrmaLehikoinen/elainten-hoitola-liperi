<?php

namespace App\Services;

use App\Models\BookingHold;
use App\Models\BookingParticipant;
use App\Models\DateCapacityOverride;
use App\Models\Resource;
use Illuminate\Support\Carbon;

/**
 * POHJA-luokka: geneerinen kapasiteettimoottori. Ei tiedä mitään
 * eläinlajeista tms. — laskee vain "mahtuuko N kappaletta resurssia
 * tyyppiä X aikavälille". $requirements-taulukoiden avain on aina
 * 'resource_type', ja kutsuvan moduulin (esim. lemmikkihoitola) vastuulla
 * on kääntää oma sanastonsa (esim. eläimen laji) tähän geneeriseen
 * avaimeen ennen kutsua.
 */
class AvailabilityService
{
    protected int $searchWindowDays = 365;
    protected int $maxResults = 365;

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
        return Resource::query()
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

            $used = BookingParticipant::query()
                ->whereRaw('LOWER(resource_type) = ?', [$resourceType])
                ->whereHas('booking', function ($query) {
                    $query->where('status', '!=', 'cancelled');
                })
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->count();

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

                $booked = BookingParticipant::query()
                    ->whereRaw('LOWER(resource_type) = ?', [$resourceType])
                    ->whereHas('booking', function ($query) {
                        $query->where('status', '!=', 'cancelled');
                    })
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->count();

                $held = (int) BookingHold::query()
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