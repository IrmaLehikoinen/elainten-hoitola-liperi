<?php

namespace App;

use App\Models\BookingParticipant;
use App\Models\Resource;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Tarkistaa onko resurssilla (esim. koirapaikat) tilaa koko pyydetylle aikavälille.
     */
    public function isAvailable(Resource $resource, string $startDate, string $endDate): bool
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $bookedCount = BookingParticipant::where('resource_id', $resource->id)
                ->whereHas('booking', function ($query) {
                    $query->where('status', '!=', 'cancelled');
                })
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->count();

            if ($bookedCount >= $resource->capacity) {
                return false;
            }
        }

        return true;
    }
}