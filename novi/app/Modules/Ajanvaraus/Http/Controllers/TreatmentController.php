<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ajanvaraus\Mail\TreatmentPaymentRequired;
use App\Modules\Ajanvaraus\Models\Treatment;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use App\Modules\Ajanvaraus\Models\TreatmentCategory;
use App\Modules\Ajanvaraus\Services\TreatmentAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class TreatmentController extends Controller
{
    public function index(Request $request, TreatmentAvailabilityService $availability)
    {
        $treatments = Treatment::orderBy('order')->orderBy('name')->get();

        $selectedTreatment = null;
        $dateSlots = [];

        if ($request->filled('booking_treatment_id')) {
            $selectedTreatment = $treatments->firstWhere('id', (int) $request->query('booking_treatment_id'));

            if ($selectedTreatment) {
                $cursor = Carbon::today();

                for ($i = 0; $i < 30; $i++) {
                    $daySlots = $availability->slotsForDate($selectedTreatment, $cursor->copy());

                    if (! empty($daySlots)) {
                        $dateSlots[$cursor->format('Y-m-d')] = collect($daySlots)->map(fn ($slot) => [
                            'iso' => $slot['start']->toDateTimeString(),
                            'label' => $cursor->copy()->translatedFormat('D j.n.').' klo '.$slot['start']->format('H:i'),
                        ])->values()->all();
                    }

                    $cursor->addDay();
                }
            }
        }

        return view('ajanvaraus::treatments.index', [
            'treatments' => $treatments,
            'categories' => TreatmentCategory::orderBy('order')->orderBy('name')->get(),
            'selectedTreatment' => $selectedTreatment,
            'dateSlots' => $dateSlots,
        ]);
    }

    public function create()
    {
        return view('ajanvaraus::treatments.form', [
            'treatment' => new Treatment(['capacity' => 1, 'is_active' => true, 'color' => '#7CAB33']),
            'categories' => TreatmentCategory::orderBy('order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['price'] = $validated['price'] ?? 0;

        $treatment = Treatment::create($validated);   

        return redirect()->route('ajanvaraus.treatments.edit', $treatment)->with('status', 'Hoito tallennettu.');
    }

    public function edit(Treatment $treatment)
    {
        return view('ajanvaraus::treatments.form', [
            'treatment' => $treatment->load(['availabilityRules', 'specialOpenings']),
            'categories' => TreatmentCategory::orderBy('order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Treatment $treatment)
    {
            $validated = $this->validated($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['price'] = $validated['price'] ?? 0;

        $treatment->update($validated);   

        return redirect()->route('ajanvaraus.treatments.edit', $treatment)->with('status', 'Hoito päivitetty.');
    }

        public function destroy(Treatment $treatment)
    {
        $treatment->delete();

        return redirect()->route('ajanvaraus.treatments.index')->with('status', 'Hoito poistettu.');
    }

    public function updateOrganizing(Request $request, Treatment $treatment)
    {
            $validated = $request->validate([
            'order' => ['required', 'integer', 'min:0'],
        ]);

        $treatment->update($validated);

        return back()->with('status', 'Hoito päivitetty.');
    }

        private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
            'treatment_category_id' => ['nullable', 'exists:treatment_categories,id'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:600'],
            'capacity' => ['required', 'integer', 'min:1'],
            'min_participants' => ['nullable', 'integer', 'min:0'],
            'warning_days_before' => ['nullable', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
        ], [
                        'short_description.max' => 'Lyhyt kuvaus voi olla enintään 1000 merkkiä pitkä.',
        ]);
    }

    public function storeAppointment(Request $request, TreatmentAvailabilityService $availability)
    {
        $validated = $request->validate([
            'treatment_id' => ['required', 'exists:treatments,id'],
            'starts_at' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'payment_choice' => ['nullable', 'in:send_link,pay_on_site'],
        ]);

        $treatment = Treatment::findOrFail($validated['treatment_id']);

        $start = Carbon::parse($validated['starts_at']);
        $end = $start->copy()->addMinutes($treatment->duration_minutes);
        $date = $start->copy()->startOfDay();

        $stillFree = collect($availability->slotsForDate($treatment, $date))
            ->contains(fn ($slot) => $slot['start']->equalTo($start));

        if (! $stillFree) {
            return back()
                ->with('registration_error', 'Valitettavasti tämä aika ehdittiin jo varata. Valitse toinen aika.')
                ->withInput();
        }

        $requiresPayment = (float) $treatment->price > 0;
        $paymentChoice = $requiresPayment ? ($validated['payment_choice'] ?? 'send_link') : null;

        if ($paymentChoice === 'send_link') {
            $twoDaysOut = now()->addDays(2);
            $paymentDeadline = $twoDaysOut->gt($start) ? $start : $twoDaysOut;

            $appointment = TreatmentAppointment::create([
                'company_id' => $treatment->company_id,
                'treatment_id' => $treatment->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'starts_at' => $start,
                'ends_at' => $end,
                'status' => 'pending',
                'price' => $treatment->price,
                'payment_choice' => 'send_link',
                'payment_deadline' => $paymentDeadline,
            ]);

            $paymentUrl = route('ajanvaraus.payment.checkout', $appointment->payment_token);
            Mail::to($appointment->email)->send(new TreatmentPaymentRequired($appointment, $paymentUrl));

            return redirect()->route('ajanvaraus.treatments.index')->with('status', 'Varaus lisätty — maksulinkki lähetetty sähköpostiin.');
        }

        TreatmentAppointment::create([
            'company_id' => $treatment->company_id,
            'treatment_id' => $treatment->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'starts_at' => $start,
            'ends_at' => $end,
            'status' => $requiresPayment ? 'pending' : 'confirmed',
            'price' => $treatment->price,
            'payment_choice' => $requiresPayment ? 'pay_on_site' : null,
            'payment_deadline' => $requiresPayment ? $start->copy()->endOfDay() : null,
        ]);

        return redirect()->route('ajanvaraus.treatments.index')->with('status', $requiresPayment ? 'Varaus lisätty — maksaa paikan päällä.' : 'Varaus lisätty ja vahvistettu.');
    }
}