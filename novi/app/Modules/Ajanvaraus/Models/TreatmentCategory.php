<?php

namespace App\Modules\Ajanvaraus\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentCategory extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'link_url',
        'order',
    ];

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class)->orderBy('order')->orderBy('name');
    }
}