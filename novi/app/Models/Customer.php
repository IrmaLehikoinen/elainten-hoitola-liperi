<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToCompany;

  protected $fillable = ['company_id', 'name', 'email', 'phone', 'address', 'notes', 'custom_daily_rate']; 

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}