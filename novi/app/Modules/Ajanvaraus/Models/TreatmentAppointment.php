<?php

namespace App\Modules\Ajanvaraus\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentAppointment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'treatment_id',
        'name',
        'email',
        'phone',
        'starts_at',
        'ends_at',
        'status',
        'price',
        'payment_method',
        'payment_choice',
        'paid_at',
        'payment_deadline',
        'payment_token',
        'cancellation_reason',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}