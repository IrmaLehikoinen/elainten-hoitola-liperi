<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
 protected $fillable = ['company_id', 'name', 'type', 'capacity'];   
public function bookingParticipants()
{
    return $this->hasMany(BookingParticipant::class);
}    
}
