<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\CourseRegistration;
use App\Services\StripeCheckoutService;

class CoursePaymentController extends Controller
{
    public function checkout(CourseRegistration $registration, StripeCheckoutService $checkout)
    {
        if ($registration->status !== 'pending' || $registration->paid_at) {
            abort(404);
        }

        $url = $checkout->createSessionUrl([
            'email' => $registration->email,
            'name' => $registration->name,
            'amount' => (float) $registration->course->price,
            'description' => 'Kurssi-ilmoittautuminen: '.$registration->course->name,
            'metadata' => ['course_registration_id' => $registration->id],
            'preferred_deadline' => $registration->payment_deadline,
            'success_url' => route('kurssit.public.success'),
            'cancel_url' => route('kurssit.public.cancelled'),
        ]);

        return redirect($url);
    }
}