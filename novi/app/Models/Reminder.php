<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'booking_participant_id',
        'pet_id',
        'customer_id',
        'type',
        'title',
        'description',
        'due_at',
        'done_at',
        'created_by',
        'done_by',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'done_at' => 'datetime',
    ];

    public function bookingParticipant()
    {
        return $this->belongsTo(BookingParticipant::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function isDone(): bool
    {
        return $this->done_at !== null;
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('due_at', $date);
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('done_at');
    }
}