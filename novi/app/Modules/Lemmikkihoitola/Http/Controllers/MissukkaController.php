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
        ]);
    }
}