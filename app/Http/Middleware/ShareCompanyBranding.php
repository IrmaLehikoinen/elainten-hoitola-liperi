<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareCompanyBranding
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $company = null;

        if (auth()->check()) {
            $company = auth()->user()->company;
        }

        View::share('currentCompany', $company);

        return $next($request);
    }
}