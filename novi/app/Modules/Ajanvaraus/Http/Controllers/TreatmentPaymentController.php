<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use App\Services\StripeCheckoutService;

class TreatmentPaymentController extends Controller
{
    public function checkout(TreatmentAppointment $appointment, StripeCheckoutService $checkout)
    {
        if ($appointment->status !== 'pending' || $appointment->paid_at) {
            abort(404);
        }

        $url = $checkout->createSessionUrl([
            'email' => $appointment->email,
            'name' => $appointment->name,
            'amount' => (float) $appointment->price,
            'description' => 'Ajanvaraus: '.$appointment->treatment->name,
            'metadata' => ['treatment_appointment_id' => $appointment->id],
            'preferred_deadline' => $appointment->payment_deadline,
            'success_url' => route('ajanvaraus.public.success', $appointment),
            'cancel_url' => route('ajanvaraus.public.book', ['treatment_id' => $appointment->treatment_id]),
        ]);

        return redirect($url);
    }
}