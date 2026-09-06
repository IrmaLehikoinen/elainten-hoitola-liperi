<?php

namespace App\Modules\Ajanvaraus\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treatment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'short_description',
        'duration_minutes',
        'capacity',
        'price',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(TreatmentAvailabilityRule::class);
    }

    public function specialOpenings(): HasMany
    {
        return $this->hasMany(TreatmentSpecialOpening::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(TreatmentAppointment::class);
    }
}