<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ActiveCompanyResolver;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    public function index()
    {
        $company = app(ActiveCompanyResolver::class)->current();
        $fontOptions = $this->fontOptions();
        $currentHeading = $company->settings['font_heading'] ?? 'Playfair Display';
        $selectedFontPair = collect($fontOptions)->search(fn ($option) => $option['heading'] === $currentHeading) ?: 'playfair_inter';

        return view('kurssit::settings.index', [
            'company' => $company,
            'fontOptions' => $fontOptions,
            'selectedFontPair' => $selectedFontPair,
        ]);
    }

    public function updateCompanyInfo(Request $request)
    {
             $validated = $request->validate([
            'official_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'business_id' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:50'],
            'payment_term_days' => ['required', 'integer', 'min:1', 'max:90'],
            'vat_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'accent_soft_color' => ['nullable', 'string', 'max:20'],
            'warm_color' => ['nullable', 'string', 'max:20'],
            'warm_light_color' => ['nullable', 'string', 'max:20'],
            'font_pair' => ['nullable', 'string', 'in:playfair_inter,montserrat_open_sans,merriweather_lato,poppins_inter'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $company = app(ActiveCompanyResolver::class)->current();

        if ($request->hasFile('logo')) {
            $company->logo_path = $request->file('logo')->store('logos', 'public');
        }   

        $settings = $company->settings ?? [];
        $settings['official_name'] = $validated['official_name'] ?? null;
        $settings['business_id'] = $validated['business_id'] ?? null;
        $settings['address'] = $validated['address'] ?? null;
        $settings['iban'] = $validated['iban'] ?? null;
        $settings['payment_term_days'] = $validated['payment_term_days'];
        $settings['accent_color'] = $validated['accent_color'] ?? null;
        $settings['accent_soft_color'] = $validated['accent_soft_color'] ?? null;
        $settings['warm_color'] = $validated['warm_color'] ?? null;
        $settings['warm_light_color'] = $validated['warm_light_color'] ?? null;

        if (! empty($validated['font_pair'])) {
            $chosen = $this->fontOptions()[$validated['font_pair']];
            $settings['font_heading'] = $chosen['heading'];
            $settings['font_body'] = $chosen['body'];
        }

        $company->settings = $settings;
        $company->phone = $validated['phone'] ?? null;
        $company->primary_color = $validated['primary_color'] ?? $company->primary_color;
        $company->secondary_color = $validated['secondary_color'] ?? $company->secondary_color;
        $company->save();

        return redirect()->route('kurssit.settings.index')->with('status', 'Yritystiedot päivitetty.');
    }

    /**
     * Valmiiksi koodatut fonttiparit. Kaikki nämä fontit ladataan aina
     * julkisen sivun layoutissa (public.blade.php), joten mikä tahansa
     * pari toimii heti kun se valitaan täältä — ei koodin muokkausta.
     */
    private function fontOptions(): array
    {
        return [
            'playfair_inter' => ['label' => 'Playfair Display + Inter (klassinen)', 'heading' => 'Playfair Display', 'body' => 'Inter'],
            'montserrat_open_sans' => ['label' => 'Montserrat + Open Sans (moderni)', 'heading' => 'Montserrat', 'body' => 'Open Sans'],
            'merriweather_lato' => ['label' => 'Merriweather + Lato (lämmin)', 'heading' => 'Merriweather', 'body' => 'Lato'],
            'poppins_inter' => ['label' => 'Poppins + Inter (pyöreä)', 'heading' => 'Poppins', 'body' => 'Inter'],
        ];
    }
}