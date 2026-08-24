<?php

namespace App\Events;

class StripeCheckoutCompleted
{
    public function __construct(
        public array $metadata,
    ) {
    }
}