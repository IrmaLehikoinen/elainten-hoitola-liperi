<?php

namespace App\Modules\Ajanvaraus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentAvailabilityRule extends Model
{
    protected $fillable = [
        'treatment_id',
        'weekday',
        'start_time',
        'end_time',
        'reminder_note',
        'reminder_date',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}