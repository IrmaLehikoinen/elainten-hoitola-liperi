<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Ajanvaraus\Models\TreatmentCategory;
use App\Modules\Kurssit\Models\Course;

class SydanpolkuController extends Controller
{
    public function index()
    {
        $company = Company::where('industry', 'kurssit')->firstOrFail();

            $courses = Course::withoutGlobalScope('company')
            ->where('company_id', $company->id)
            ->whereNull('cancelled_at')
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '>=', now());
            })
            ->orderBy('starts_at')
            ->take(3)
            ->get();

            $treatmentCategories = TreatmentCategory::withoutGlobalScope('company')
            ->where('company_id', $company->id)
            ->with(['treatments' => function ($query) {
                $query->withoutGlobalScope('company');
            }])
            ->orderBy('order')
            ->get();

        return view('kurssit::public.sydanpolku', [
            'courses' => $courses,
            'company' => $company,
            'treatmentCategories' => $treatmentCategories,
        ]);
    }

    public function tyohyvinvointi()
    {
        $company = Company::where('industry', 'kurssit')->firstOrFail();

        return view('kurssit::public.tyohyvinvointi', [
            'company' => $company,
        ]);
    }
}