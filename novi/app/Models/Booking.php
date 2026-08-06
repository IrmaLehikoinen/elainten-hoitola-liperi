<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'company_id',
        'customer_id',
        'arrival_at',
        'pickup_at',
        'care_type',
        'responsible_user_id',
        'resource_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'priority',
        'confirmation_channel',
        'total_price',
        'price_estimate',
        'deposit_amount',
        'deposit_paid_at',
        'locked_until',
        'send_email_confirmation',
        'send_sms_confirmation',
        'notes',
    ];

    protected $casts = [
        'arrival_at' => 'datetime',
        'pickup_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
        'price_estimate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_paid_at' => 'datetime',
        'locked_until' => 'datetime',
        'send_email_confirmation' => 'boolean',
        'send_sms_confirmation' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function participants()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function historyEntries()
    {
        return $this->hasMany(PetHistoryEntry::class);
    }
}