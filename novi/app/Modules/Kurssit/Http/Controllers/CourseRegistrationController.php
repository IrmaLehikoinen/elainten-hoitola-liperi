<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Kurssit\Mail\CourseRegistrationConfirmed;
use App\Modules\Kurssit\Models\Course;
use App\Modules\Kurssit\Models\CourseRegistration;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CourseRegistrationController extends Controller
{
    public function index()
    {
        $company = Company::where('industry', 'kurssit')->firstOrFail();

        $courses = Course::where('company_id', $company->id)
            ->orderBy('starts_at')
            ->get();

        return view('kurssit::public.index', [
            'courses' => $courses,
        ]);
    }

    public function show(Course $course)
    {
        return view('kurssit::public.register', [
            'course' => $course,
        ]);
    }

    public function store(Request $request, Course $course, StripeCheckoutService $checkout)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        if ($course->remainingSpots() <= 0) {
            return back()
                ->with('registration_error', 'Valitettavasti kurssi on juuri täyttynyt.')
                ->withInput();
        }

        $requiresPayment = (float) $course->price > 0;

        $registration = CourseRegistration::create([
            'company_id' => $course->company_id,
            'course_id' => $course->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $requiresPayment ? 'pending' : 'confirmed',
            'payment_deadline' => $requiresPayment ? now()->addMinutes(30) : null,
        ]);

        // Uudelleentarkistus juuri tallennuksen jälkeen: jos kaksi ilmoittautumista
        // ehti mennä läpi samaan aikaan viimeiselle paikalle, perutaan tämä.
        if ($course->remainingSpots() < 0) {
            $registration->delete();

            return back()
                ->with('registration_error', 'Valitettavasti kurssi täyttyi juuri ennen ilmoittautumisesi vahvistumista.')
                ->withInput();
        }

        if (! $requiresPayment) {
            Mail::to($registration->email)->send(new CourseRegistrationConfirmed($registration));

            return redirect()->route('kurssit.public.success');
        }

                $url = $checkout->createSessionUrl([
            'email' => $registration->email,
            'name' => $registration->name,
            'amount' => (float) $course->price,
            'description' => 'Kurssi-ilmoittautuminen: '.$course->name,
            'metadata' => ['course_registration_id' => $registration->id],
            'preferred_deadline' => $registration->payment_deadline,
            'success_url' => route('kurssit.public.success'),
            'cancel_url' => route('kurssit.public.cancelled'),
        ]);

        return view('kurssit::public.redirecting', ['url' => $url]);
    }
}