<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetHistoryEntry extends Model
{
    protected $fillable = [
        'pet_id',
        'booking_id',
        'entry_date',
        'stay_start',
        'stay_end',
        'category',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'stay_start' => 'datetime',
        'stay_end' => 'datetime',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}