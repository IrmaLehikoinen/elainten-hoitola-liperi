<?php

namespace App\Events;

class SchedulingConflictDetected
{
    public function __construct(
        public int $companyId,
        public string $title,
        public ?string $description = null,
        public ?\Carbon\Carbon $date = null,
        public ?string $url = null,
    ) {
    }
}