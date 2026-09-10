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
        'warning_sent_at',
        'price',
        'payment_method',
        'payment_choice',
        'paid_at',
        'payment_deadline',
        'payment_token',
        'cancellation_reason',
        'cancelled_at',
        'notes',
        'invoice_number',
        'issued_at',
        'refunded_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'warning_sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
        'issued_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function referenceNumber(): string
    {
        $base = str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
        $weights = [7, 3, 1];
        $sum = 0;

        foreach (str_split(strrev($base)) as $index => $digit) {
            $sum += ((int) $digit) * $weights[$index % 3];
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $base.$checkDigit;
    }

    public function dueDate()
    {
        $days = (int) ($this->company->settings['payment_term_days'] ?? 14);

        return $this->issued_at?->copy()->addDays($days);
    }
}