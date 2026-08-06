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
        'species',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
}