<?php

namespace App\Http\Middleware;

use App\Core\Branding\BrandManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareCompanyBranding
{
    public function __construct(
        private BrandManager $brandManager
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        View::share('brand', $this->brandManager->current());

        $activeCompany = app(\App\Services\ActiveCompanyResolver::class)->current();

            View::share('company', $activeCompany
            ? ['id' => $activeCompany->id, 'name' => $activeCompany->name, 'industry' => $activeCompany->industry]
            : $this->brandManager->company());    

        return $next($request);
    }
}