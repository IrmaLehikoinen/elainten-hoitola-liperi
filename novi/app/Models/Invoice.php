<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'booking_id',
        'customer_id',
        'invoice_number',
        'line_items',
        'subtotal',
        'vat_percentage',
        'vat_amount',
        'deposit_amount',
        'total_due',
        'issued_at',
    ];

    protected $casts = [
        'line_items' => 'array',
        'subtotal' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'total_due' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

   public function customer()
    {
        return $this->belongsTo(Customer::class);
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

        return $base . $checkDigit;
    }

    public function dueDate()
    {
        $days = (int) ($this->company->settings['payment_term_days'] ?? 14);

        return $this->issued_at?->copy()->addDays($days);
    }
}