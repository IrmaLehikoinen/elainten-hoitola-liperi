<?php

namespace App\Events;

use Illuminate\Support\Carbon;

class CompanyDateReopened
{
    public function __construct(
        public int $companyId,
        public Carbon $date,
    ) {
    }
}