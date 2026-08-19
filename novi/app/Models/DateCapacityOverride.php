<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DateCapacityOverride extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'date',
        'species',
        'capacity',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}