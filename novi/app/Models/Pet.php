<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'species',
        'breed',
        'birth_date',
        'sex',
        'weight',
        'microchip_number',
        'vaccinations',
        'allergies',
        'medications',
        'feeding_instructions',
        'behaviour_notes',
        'veterinarian_name',
        'veterinarian_phone',
        'emergency_notes',
        'general_notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bookingParticipants()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function historyEntries()
    {
        return $this->hasMany(PetHistoryEntry::class)
            ->latest('entry_date');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}