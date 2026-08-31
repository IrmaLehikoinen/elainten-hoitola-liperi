<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Services\ActiveCompanyResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Rajaa mallin kyselyt automaattisesti kirjautuneen käyttäjän AKTIIVISEEN
 * yritykseen (ks. App\Services\ActiveCompanyResolver) ja täyttää
 * company_id:n automaattisesti uutta riviä luotaessa.
 *
 * Käyttäjällä joka kuuluu vain yhteen yritykseen (tavallinen tilanne),
 * aktiivinen yritys on aina hänen oma kotiyrityksensä — käyttäytyminen
 * on identtinen aiempaan verrattuna.
 *
 * Huom: älä käytä tätä traittia User-mallissa - se aiheuttaisi
 * kanan ja munan ongelman kirjautumisen yhteydessä.
 */
trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (! Auth::check()) {
                return;
            }

            $activeCompany = app(ActiveCompanyResolver::class)->current();

            if ($activeCompany) {
                $builder->where(
                    $builder->getModel()->getTable().'.company_id',
                    $activeCompany->id
                );
            }
        });

        static::creating(function ($model) {
            if (! empty($model->company_id) || ! Auth::check()) {
                return;
            }

            $activeCompany = app(ActiveCompanyResolver::class)->current();

            if ($activeCompany) {
                $model->company_id = $activeCompany->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}