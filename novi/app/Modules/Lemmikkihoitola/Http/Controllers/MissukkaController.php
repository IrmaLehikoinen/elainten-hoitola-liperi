<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;

class MissukkaController extends Controller
{
    public function index()
    {
        $company = Company::where('industry', 'lemmikkihoitola')->firstOrFail();

        return view('public.missukka', [
            'company' => $company,
            'careContractText' => $this->careContractTextFor($company),
        ]);
    }

    private function careContractTextFor(Company $company): ?string
    {
        $enabled = $company->settings['care_contract_enabled'] ?? true;
        $text = $company->settings['care_contract_text'] ?? null;

        return ($enabled && filled($text)) ? $text : null;
    }
}