<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Core\Branding\BrandManager;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use App\Modules\Ajanvaraus\Services\TreatmentAvailabilityService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class PublicBookingController extends Controller
{
    public function __construct(private TreatmentAvailabilityService $availability)
    {
    }

    public function index(Request $request)
    {
        $company = Company::whereJsonContains('active_modules', 'ajanvaraus')->firstOrFail();

        $this->shareCompanyBrand($company);

        $treatments = Treatment::withoutGlobalScope('company')
            ->with('category')
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $treatment = null;
        $dateSlots = [];

        if ($request->filled('treatment_id')) {
            $treatment = $treatments->firstWhere('id', (int) $request->query('treatment_id'));

            if ($treatment) {
                $cursor = Carbon::today();
                for ($i = 0; $i < 60; $i++) {
                    $daySlots = $this->availability->slotsForDate($treatment, $cursor->copy());

                    if (! empty($daySlots)) {
                        $dateSlots[$cursor->format('Y-m-d')] = collect($daySlots)->map(fn ($slot) => [
                            'iso' => $slot['start']->toDateTimeString(),
                            'label' => $slot['start']->format('H:i'),
                        ])->values()->all();
                    }

                    $cursor->addDay();
                }
            }
        }

        $upcomingSlots = collect($dateSlots)
            ->flatMap(fn ($slots, $date) => collect($slots)->map(fn ($slot) => array_merge($slot, [
                'dateLabel' => Carbon::parse($date)->translatedFormat('D j.n.'),
            ])))
            ->take(10)
            ->values()
            ->all();

            return view('ajanvaraus::public.book', [
            'company' => $company,
            'treatments' => $treatments,
            'treatment' => $treatment,
            'dateSlots' => $dateSlots,
            'upcomingSlots' => $upcomingSlots,
            'name' => $request->query('name', ''),
            'email' => $request->query('email', ''),
            'phone' => $request->query('phone', ''),
        ]);
    }

    public function store(Request $request, StripeCheckoutService $checkout)
    {
        $validated = $request->validate([
            'treatment_id' => ['required', 'exists:treatments,id'],
            'starts_at' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $treatment = Treatment::withoutGlobalScope('company')->findOrFail($validated['treatment_id']);

        if (! $treatment->is_active) {
            abort(404);
        }

        $start = Carbon::parse($validated['starts_at']);
        $end = $start->copy()->addMinutes($treatment->duration_minutes);
        $date = $start->copy()->startOfDay();

        $stillFree = collect($this->availability->slotsForDate($treatment, $date))
            ->contains(fn ($slot) => $slot['start']->equalTo($start));

        if (! $stillFree) {
            return back()->withErrors(['starts_at' => 'Valitettavasti tämä aika ehdittiin jo varata. Valitse toinen aika.']);
        }

        $requiresPayment = (float) $treatment->price > 0;

        $appointment = TreatmentAppointment::create([
            'company_id' => $treatment->company_id,
            'treatment_id' => $treatment->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'starts_at' => $start,
            'ends_at' => $end,
            'status' => $requiresPayment ? 'pending' : 'confirmed',
            'price' => $treatment->price,
            'payment_deadline' => $requiresPayment ? now()->addMinutes(30) : null,
        ]);

        if (! $requiresPayment) {
            return redirect()->route('ajanvaraus.public.success', $appointment);
        }

        $url = $checkout->createSessionUrl([
            'email' => $appointment->email,
            'name' => $appointment->name,
            'amount' => (float) $treatment->price,
            'description' => 'Ajanvaraus: '.$treatment->name,
            'metadata' => ['treatment_appointment_id' => $appointment->id],
            'preferred_deadline' => $appointment->payment_deadline,
            'success_url' => route('ajanvaraus.public.success', $appointment),
            'cancel_url' => route('ajanvaraus.public.book', ['treatment_id' => $treatment->id]),
        ]);

        return redirect($url);
    }

    public function success(int $appointment)
    {
        $appointment = TreatmentAppointment::withoutGlobalScope('company')
            ->with(['treatment' => fn ($query) => $query->withoutGlobalScope('company')])
            ->findOrFail($appointment);

        $this->shareCompanyBrand($appointment->treatment->company);

        return view('ajanvaraus::public.success', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Jakaa nimenomaan TÄMÄN yrityksen brändivärit näkymälle — ei voida
     * luottaa pohjan ActiveCompanyResolveriin, koska se toimii vain
     * kirjautuneelle käyttäjälle. Julkisella (uloskirjautuneella)
     * varaussivulla pitää silti näkyä juuri tämän asiakkaan omat värit.
     */
    private function shareCompanyBrand(Company $company): void
    {
        $brand = app(BrandManager::class)->current();
        $settings = $company->settings ?? [];

        $brand = array_merge($brand, array_filter([
            'name' => $company->name,
            'primary_color' => $company->primary_color,
            'secondary_color' => $company->secondary_color,
            'font_heading' => $settings['font_heading'] ?? null,
            'accent_color' => $settings['accent_color'] ?? null,
            'accent_soft_color' => $settings['accent_soft_color'] ?? null,
            'warm_color' => $settings['warm_color'] ?? null,
            'warm_light_color' => $settings['warm_light_color'] ?? null,
            'logo' => $company->logo_path,
        ]));

        View::share('brand', $brand);
    }
}