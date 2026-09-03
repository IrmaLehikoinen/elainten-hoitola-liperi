<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Kurssit\Models\Course;

class SydanpolkuController extends Controller
{
    public function index()
    {
        $company = Company::where('industry', 'kurssit')->firstOrFail();

        $courses = Course::where('company_id', $company->id)
            ->whereNull('cancelled_at')
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '>=', now());
            })
            ->orderBy('starts_at')
            ->take(3)
            ->get();

        return view('kurssit::public.sydanpolku', [
            'courses' => $courses,
        ]);
    }
}