<?php

namespace App\Models\Concerns;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Rajaa mallin kyselyt automaattisesti kirjautuneen käyttäjän yritykseen
 * ja täyttää company_id:n automaattisesti uutta riviä luotaessa.
 *
 * Huom: älä käytä tätä traittia User-mallissa - se aiheuttaisi
 * kanan ja munan ongelman kirjautumisen yhteydessä.
 */
trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check() && Auth::user()->company_id) {
                $builder->where(
                    $builder->getModel()->getTable().'.company_id',
                    Auth::user()->company_id
                );
            }
        });

        static::creating(function ($model) {
            if (empty($model->company_id) && Auth::check() && Auth::user()->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}