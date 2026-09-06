<?php

namespace App\Modules\Ajanvaraus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentHold extends Model
{
    protected $fillable = [
        'treatment_id',
        'starts_at',
        'ends_at',
        'expires_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}