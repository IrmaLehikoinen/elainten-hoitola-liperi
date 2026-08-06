<?php

namespace App\Http\Middleware;

use App\Core\Branding\BrandManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareCompanyBranding
{
    public function handle(
        Request $request,
        Closure $next,
        BrandManager $brandManager
    ): Response {
        View::share('brand', $brandManager->current());

        return $next($request);
    }
}