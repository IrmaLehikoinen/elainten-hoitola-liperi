<?php

namespace App\Modules\Lemmikkihoitola\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantService extends Model
{
    protected $fillable = ['booking_participant_id', 'service_id', 'price'];
}