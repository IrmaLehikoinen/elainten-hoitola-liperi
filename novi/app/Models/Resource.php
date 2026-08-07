<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'type', 'capacity'];

    public function bookingParticipants()
    {
        return $this->hasMany(BookingParticipant::class);
    }
}