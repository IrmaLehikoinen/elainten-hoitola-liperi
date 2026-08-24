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
        'resource_type',
        'capacity',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}