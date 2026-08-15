<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CareType extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'slug', 'label', 'sort_order'];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'care_type', 'slug');
    }
}