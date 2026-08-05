<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantService extends Model
{
    protected $fillable = ['booking_participant_id', 'service_id', 'price'];
}
