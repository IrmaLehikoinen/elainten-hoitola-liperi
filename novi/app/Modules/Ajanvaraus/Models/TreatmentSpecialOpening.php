<?php

namespace App\Modules\Ajanvaraus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentSpecialOpening extends Model
{
    protected $fillable = [
        'treatment_id',
        'date',
        'start_time',
        'end_time',
        'reminder_note',
        'reminder_date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}