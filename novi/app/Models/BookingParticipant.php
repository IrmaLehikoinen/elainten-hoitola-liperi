<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingParticipant extends Model
{
 protected $fillable = ['booking_id', 'resource_id', 'name', 'species', 'start_date', 'end_date', 'notes'];   
 public function booking()
{
    return $this->belongsTo(Booking::class);
}

public function resource()
{
    return $this->belongsTo(Resource::class);
}   
}
