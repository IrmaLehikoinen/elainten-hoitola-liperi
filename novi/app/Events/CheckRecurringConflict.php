<?php

namespace App\Events;

use Illuminate\Support\Carbon;

class CheckRecurringConflict
{
    public array $conflicts = [];

    public function __construct(
        public int $companyId,
        public Carbon $start,
        public Carbon $end,
    ) {
    }
}