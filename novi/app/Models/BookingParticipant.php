<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingParticipant extends Model
{
    protected $fillable = [
        'booking_id',
        'pet_id',
        'resource_id',
        'name',
        'resource_type',
        'start_date',
        'end_date',
        'daily_rate',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_rate' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function services()
    {
        return $this->hasMany(ParticipantService::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * Hoitopäivien lukumäärä (alkaen ja päättyen mukaan lukien).
     */
    public function careDays(): int
    {
        if (! $this->start_date || ! $this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Eläimen hoito-osuuden hinta (vrk-hinta x vrk-määrä).
     */
    public function careSubtotal(): float
    {
        return (float) $this->daily_rate * $this->careDays();
    }
}