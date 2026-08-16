<?php

namespace App\Http\Controllers;

use App\Models\CareType;
use App\Models\Reminder;
use App\Models\ReminderType;
use App\Models\Resource;
use App\Models\Service;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    /**
     * Yritysasetukset-sivu: eläinryhmät/kapasiteetti, lisäpalvelut,
     * muistutustyypit ja hoitomuodot yhdestä paikasta muokattavaksi.
     */
    public function index()
    {
        return view('settings.index', [
            'company' => request()->user()->company,
            'resources' => Resource::orderBy('type')->get(),
            'services' => Service::orderBy('name')->get(),
            'reminderTypes' => ReminderType::orderBy('sort_order')->get(),
            'careTypes' => CareType::orderBy('sort_order')->get(),
        ]);
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

        return back()->with('status', 'Varausmaksun prosentti päivitetty.');
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

        return back()->with('status', 'Perushinta päivitetty.');
    }

    public function storeResource(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:0'],
        ]);

        Resource::create($validated);

        return back()->with('status', 'Eläinryhmä lisätty.');
    }

    public function updateResource(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:0'],
        ]);

        $resource->update($validated);

        return back()->with('status', 'Eläinryhmä päivitetty.');
    }

    public function destroyResource(Resource $resource)
    {
        $resource->delete();

        return back()->with('status', 'Eläinryhmä poistettu.');
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

        return back()->with('status', 'Palvelu lisätty.');
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

        return back()->with('status', 'Palvelu päivitetty.');
    }

    public function destroyService(Service $service)
    {
        $service->delete();

        return back()->with('status', 'Palvelu poistettu.');
    }

    public function storeReminderType(Request $request)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        ReminderType::create($validated);

        return back()->with('status', 'Muistutustyyppi lisätty.');
    }

    public function updateReminderType(Request $request, ReminderType $reminderType)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $reminderType->update($validated);

        return back()->with('status', 'Muistutustyyppi päivitetty.');
    }

    public function destroyReminderType(ReminderType $reminderType)
    {
        if (Reminder::where('type', $reminderType->slug)->exists()) {
            return back()->with('error', 'Tyyppiä ei voi poistaa, koska sitä käyttäviä muistutuksia on jo olemassa.');
        }

        $reminderType->delete();

        return back()->with('status', 'Muistutustyyppi poistettu.');
    }

    public function storeCareType(Request $request)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        CareType::create($validated);

        return back()->with('status', 'Hoitomuoto lisätty.');
    }

    public function updateCareType(Request $request, CareType $careType)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $careType->update($validated);

        return back()->with('status', 'Hoitomuoto päivitetty.');
    }

    public function destroyCareType(CareType $careType)
    {
        $careType->delete();

        return back()->with('status', 'Hoitomuoto poistettu.');
    }
}