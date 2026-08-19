<?php

namespace App\Services;

use App\Models\BookingHold;
use App\Models\BookingParticipant;
use App\Models\DateCapacityOverride;
use App\Models\Resource;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    protected int $searchWindowDays = 90;
    protected int $maxResults = 20;

    /**
     * Palauttaa listan [{iso: 'Y-m-d', display: 'd.m.Y'}] -olioita niistä
     * aloituspäivistä, joista alkaen koko hoitojakso mahtuu resursseihin.
     */
    public function findStartDates(array $requirements, int $durationDays): array
    {
        if (empty($requirements) || $durationDays < 1) {
            return [];
        }

        // Normalisoidaan lajit pieniksi kirjaimiksi, jotta "Koira" ja "koira" täsmäävät.
        $requirements = collect($requirements)
            ->map(fn ($r) => ['species' => mb_strtolower(trim($r['species'])), 'count' => $r['count']])
            ->all();

        $capacities = $this->speciesCapacities();

        foreach ($requirements as $requirement) {
            $capacity = $capacities[$requirement['species']] ?? 0;

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
     * Kokonaiskapasiteetti eläinlajeittain, avaimet pienillä kirjaimilla.
     * Tämä on se yksi paikka (resources-taulu) mistä kapasiteetti luetaan.
     */
    protected function speciesCapacities(): array
    {
        return Resource::query()
            ->get(['type', 'capacity'])
            ->groupBy(fn ($resource) => mb_strtolower(trim($resource->type)))
            ->map(fn ($group) => (int) $group->sum('capacity'))
            ->all();
    }

    protected function capacityForDate(string $species, Carbon $date, int $default): int
    {
        $override = DateCapacityOverride::query()
            ->whereDate('date', $date->toDateString())
            ->where(function ($query) use ($species) {
                $query->where('species', $species)->orWhereNull('species');
            })
            ->orderByRaw('species IS NULL')
            ->first();

        return $override ? (int) $override->capacity : $default;
    }

    public function usageForDate(Carbon $date): array
    {
        $capacities = $this->speciesCapacities();
        $result = [];

        foreach ($capacities as $species => $defaultCapacity) {
            $capacity = $this->capacityForDate($species, $date, $defaultCapacity);

            $used = BookingParticipant::query()
                ->whereRaw('LOWER(species) = ?', [$species])
                ->whereHas('booking', function ($query) {
                    $query->where('status', '!=', 'cancelled');
                })
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->count();

            $result[$species] = [
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
            $species = $requirement['species'];
            $needed = $requirement['count'];
            $defaultCapacity = $capacities[$species] ?? 0;

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $capacity = $this->capacityForDate($species, $date, $defaultCapacity);

                $booked = BookingParticipant::query()
                    ->whereRaw('LOWER(species) = ?', [$species])
                    ->whereHas('booking', function ($query) {
                        $query->where('status', '!=', 'cancelled');
                    })
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->count();

                $held = (int) BookingHold::query()
                    ->whereRaw('LOWER(species) = ?', [$species])
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
}