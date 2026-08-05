<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
  protected $fillable = ['company_id', 'customer_id', 'start_date', 'end_date', 'status', 'total_price', 'deposit_amount', 'deposit_paid_at', 'notes'];  
    public function participants()
{
    return $this->hasMany(BookingParticipant::class);
}
}
