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
        'deposit_amount',
        'total_due',
        'issued_at',
    ];

    protected $casts = [
        'line_items' => 'array',
        'subtotal' => 'decimal:2',
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
}