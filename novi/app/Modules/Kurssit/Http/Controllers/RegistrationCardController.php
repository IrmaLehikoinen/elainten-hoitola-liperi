<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\CourseRegistration;

class RegistrationCardController extends Controller
{
    public function show(CourseRegistration $registration)
    {
        $registration->load('course');

        return view('kurssit::cards.registration', [
            'registration' => $registration,
        ]);
    }

    public function cancel(CourseRegistration $registration)
    {
        $registration->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'manual',
        ]);

        return redirect()
            ->route('kurssit.cards.show', $registration->course_id)
            ->with('status', 'Ilmoittautuminen peruttu.');
    }
}