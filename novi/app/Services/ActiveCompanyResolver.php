<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * POHJA-palvelu: päättää kumpi käyttäjän yrityksistä on juuri nyt
 * "aktiivinen" hallintapaneelissa. Oletusarvo on aina käyttäjän oma
 * kotiyritys (users.company_id). Jos käyttäjällä on pääsy useampaan
 * yritykseen (ks. User::accessibleCompanies()), hän voi vaihtaa
 * aktiivista yritystä switchTo()-metodilla, joka tallentaa valinnan
 * istuntoon vain jos käyttäjällä on oikeasti oikeus siihen yritykseen.
 */
class ActiveCompanyResolver
{
    protected const SESSION_KEY = 'active_company_id';

    public function current(): ?Company
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        $selectedId = Session::get(self::SESSION_KEY);

        if ($selectedId) {
            $selected = $user->accessibleCompanies()->firstWhere('id', $selectedId);

            if ($selected) {
                return $selected;
            }
        }

        return $user->company;
    }

    public function switchTo(Company $company): bool
    {
        $user = Auth::user();

        if (! $user || ! $user->accessibleCompanies()->contains('id', $company->id)) {
            return false;
        }

        Session::put(self::SESSION_KEY, $company->id);

        return true;
    }
}