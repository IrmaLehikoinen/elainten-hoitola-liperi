<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class BookingHold extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'species',
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