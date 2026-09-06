<?php

namespace App\Events;

use Illuminate\Support\Carbon;

class CompanyDateClosed
{
    public function __construct(
        public int $companyId,
        public Carbon $date,
        public ?string $note = null,
    ) {
    }
}