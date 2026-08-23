<?php

namespace App\Modules\Lemmikkihoitola\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class BookingHold extends Model
{
    use BelongsToCompany;

        protected $fillable = [
        'company_id',
        'resource_type',
        'quantity',
        'start_date',
        'end_date',
        'expires_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expires_at' => 'datetime',
    ];
}