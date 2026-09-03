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
        $activeCompany = app(\App\Services\ActiveCompanyResolver::class)->current();

        $brand = $this->brandManager->current();

        if ($activeCompany) {
            $settings = $activeCompany->settings ?? [];

                     $brand = array_merge($brand, array_filter([
                'name' => $activeCompany->name,
                'primary_color' => $activeCompany->primary_color,
                'secondary_color' => $activeCompany->secondary_color,
                'font_heading' => $settings['font_heading'] ?? null,
                'font_body' => $settings['font_body'] ?? null,
                'logo' => $activeCompany->logo_path,
            ]));   
        }

        View::share('brand', $brand);

        View::share('company', $activeCompany
            ? ['id' => $activeCompany->id, 'name' => $activeCompany->name, 'industry' => $activeCompany->industry]
            : $this->brandManager->company());

        return $next($request);
    }
}