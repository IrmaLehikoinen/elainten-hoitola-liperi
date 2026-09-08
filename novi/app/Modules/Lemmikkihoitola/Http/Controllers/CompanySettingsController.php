<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lemmikkihoitola\Models\CareType;
use App\Modules\Lemmikkihoitola\Models\Reminder;
use App\Modules\Lemmikkihoitola\Models\ReminderType;
use App\Models\Resource;
use App\Modules\Lemmikkihoitola\Models\Service;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    /**
     * Yritysasetukset-sivu kokonaan lemmikkihoitola-moduulin vastuulla.
     * Myös yritystiedot (Y-tunnus, IBAN, ALV, maksuehto, brändivärit) kuuluvat
     * tänne, koska niitä käyttää yksinomaan laskutus (Invoice/InvoiceController),
     * joka on moduulin asia — pohja ei tarvitse näitä mihinkään.
     */
    public function index()
    {
        $company = request()->user()->company;
        $bookingFields = config('public_booking_fields');
        $enabledBookingFields = $company->settings['public_booking_fields'] ?? array_keys($bookingFields);

        return view('settings.index', [
            'company' => $company,
            'resources' => Resource::orderBy('type')->get(),
            'services' => Service::orderBy('name')->get(),
            'reminderTypes' => ReminderType::orderBy('sort_order')->get(),
            'careTypes' => CareType::orderBy('sort_order')->get(),
            'bookingFields' => $bookingFields,
            'enabledBookingFields' => $enabledBookingFields,
        ]);
    }

    protected function backToTab(Request $request)
    {
        return redirect()->route('admin.settings.index', ['tab' => $request->query('tab', 'perushinta')]);
    }

    public function updateDepositSettings(Request $request)
    {
        $validated = $request->validate([
            'deposit_percentage' => ['required', 'integer', 'in:0,20,30,50'],
        ]);

        $company = $request->user()->company;
        $settings = $company->settings ?? [];
        $settings['deposit_percentage'] = $validated['deposit_percentage'];
        $company->settings = $settings;
        $company->save();

        return $this->backToTab($request)->with('status', 'Varausmaksun prosentti päivitetty.');
    }

    public function updateBaseRate(Request $request)
    {
        $validated = $request->validate([
            'base_daily_rate' => ['required', 'numeric', 'min:0'],
        ]);

        $company = $request->user()->company;
        $settings = $company->settings ?? [];
        $settings['base_daily_rate'] = $validated['base_daily_rate'];
        $company->settings = $settings;
        $company->save();

        return $this->backToTab($request)->with('status', 'Perushinta päivitetty.');
    }

         public function updateCompanyInfo(Request $request)
    {
        $validated = $request->validate([
            'official_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'business_id' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:50'],
            'payment_term_days' => ['required', 'integer', 'min:1', 'max:90'],
            'vat_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'max:2048'],
                        'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'care_contract_text' => ['nullable', 'string'],
            'care_contract_enabled' => ['nullable', 'boolean'],
        ]);

        $company = $request->user()->company;
        $settings = $company->settings ?? [];
        $settings['official_name'] = $validated['official_name'] ?? null;
        $settings['business_id'] = $validated['business_id'] ?? null;
        $settings['address'] = $validated['address'] ?? null;
        $settings['iban'] = $validated['iban'] ?? null;
        $settings['payment_term_days'] = $validated['payment_term_days'];
        $settings['vat_percentage'] = $validated['vat_percentage'];
        $settings['facebook_url'] = $validated['facebook_url'] ?? null;
        $settings['instagram_url'] = $validated['instagram_url'] ?? null;
        $settings['care_contract_text'] = $validated['care_contract_text'] ?? null;
        $settings['care_contract_enabled'] = $request->boolean('care_contract_enabled');
        $company->settings = $settings;
        $company->phone = $validated['phone'] ?? null;
        $company->email = $validated['email'] ?? null;
        $company->primary_color = $validated['primary_color'] ?? $company->primary_color;
        $company->secondary_color = $validated['secondary_color'] ?? $company->secondary_color;

        if ($request->hasFile('logo')) {
            $company->logo_path = $request->file('logo')->store('logos', 'public');
        }

        $company->save();

        return $this->backToTab($request)->with('status', 'Yritystiedot päivitetty.');
    }  

    public function storeResource(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:0'],
        ]);

        Resource::create($validated);

        return $this->backToTab($request)->with('status', 'Lemmikkiryhmä lisätty.');
    }

    public function updateResource(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:0'],
        ]);

        $resource->update($validated);

        return $this->backToTab($request)->with('status','Lemmikkiryhmä päivitetty.' );
    }

    public function destroyResource(Request $request, Resource $resource)
    {
        $resource->delete();

        return $this->backToTab($request)->with('status', 'Lemmikkiryhmä poistettu.');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'pricing_type' => ['required', 'string', 'in:per_day,per_booking,fixed'],
        ]);

        Service::create($validated);

        return $this->backToTab($request)->with('status', 'Palvelu lisätty.');
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'pricing_type' => ['required', 'string', 'in:per_day,per_booking,fixed'],
        ]);

        $service->update($validated);

        return $this->backToTab($request)->with('status', 'Palvelu päivitetty.');
    }

    public function destroyService(Request $request, Service $service)
    {
        $service->delete();

        return $this->backToTab($request)->with('status', 'Palvelu poistettu.');
    }

    public function storeReminderType(Request $request)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        ReminderType::create($validated);

        return $this->backToTab($request)->with('status', 'Muistutustyyppi lisätty.');
    }

    public function updateReminderType(Request $request, ReminderType $reminderType)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $reminderType->update($validated);

        return $this->backToTab($request)->with('status', 'Muistutustyyppi päivitetty.');
    }

    public function destroyReminderType(Request $request, ReminderType $reminderType)
    {
        if (Reminder::where('type', $reminderType->slug)->exists()) {
            return $this->backToTab($request)->with('error', 'Tyyppiä ei voi poistaa, koska sitä käyttäviä muistutuksia on jo olemassa.');
        }

        $reminderType->delete();

        return $this->backToTab($request)->with('status', 'Muistutustyyppi poistettu.');
    }

    public function storeCareType(Request $request)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        CareType::create($validated);

        return $this->backToTab($request)->with('status', 'Hoitomuoto lisätty.');
    }

    public function updateCareType(Request $request, CareType $careType)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $careType->update($validated);

        return $this->backToTab($request)->with('status', 'Hoitomuoto päivitetty.');
    }

    public function destroyCareType(Request $request, CareType $careType)
    {
        $careType->delete();

        return $this->backToTab($request)->with('status', 'Hoitomuoto poistettu.');
    }

    public function updatePublicBookingFields(Request $request)
    {
        $allKeys = array_keys(config('public_booking_fields'));

        $validated = $request->validate([
            'fields' => ['nullable', 'array'],
            'fields.*' => ['string', 'in:' . implode(',', $allKeys)],
        ]);

        $company = $request->user()->company;
        $settings = $company->settings ?? [];
        $settings['public_booking_fields'] = $validated['fields'] ?? [];
        $company->settings = $settings;
        $company->save();

        return $this->backToTab($request)->with('status', 'Ajanvarauslomakkeen kentät päivitetty.');
    }
}