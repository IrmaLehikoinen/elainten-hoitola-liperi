<?php

namespace App\Modules\Ajanvaraus\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CalendarBlock extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}