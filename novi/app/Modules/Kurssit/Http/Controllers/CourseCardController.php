<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Mail\CoursePaymentRequired;
use App\Modules\Kurssit\Mail\CourseRegistrationConfirmed;
use App\Modules\Kurssit\Models\Course;
use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CourseCardController extends Controller
{
    public function index()
    {
        $courses = Course::withCount(['registrations as confirmed_count' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->orderByDesc('starts_at')
            ->get();

        $upcoming = $courses->filter(fn ($course) => ! $course->starts_at || $course->starts_at->isFuture())
            ->sortBy('starts_at')
            ->values();

        $past = $courses->filter(fn ($course) => $course->starts_at && $course->starts_at->isPast())
            ->values();

        return view('kurssit::cards.index', [
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

    public function show(Course $course)
    {
        $course->load(['registrations' => function ($query) {
            $query->orderBy('created_at');
        }]);

        return view('kurssit::cards.show', [
            'course' => $course,
        ]);
    }

    public function storeRegistration(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'payment_choice' => ['nullable', 'in:paid_now,send_link,pay_on_day'],
        ]);

        $requiresPayment = (float) $course->price > 0;
        $paymentChoice = $requiresPayment ? ($validated['payment_choice'] ?? 'send_link') : 'paid_now';

        // Vaihtoehto 1: maksulinkki sähköpostiin, sama kaava kuin
        // Lemmikkihoitolan käsin syötetyssä varauksessa.
        if ($paymentChoice === 'send_link') {
            $twoDaysOut = now()->addDays(2);
            $paymentDeadline = ($course->starts_at && $twoDaysOut->gt($course->starts_at))
                ? $course->starts_at
                : $twoDaysOut;

            $registration = CourseRegistration::create([
                'company_id' => $course->company_id,
                'course_id' => $course->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => 'pending',
                'payment_choice' => 'send_link',
                'payment_deadline' => $paymentDeadline,
            ]);

            if ($registration->email) {
                $paymentUrl = route('kurssit.payment.checkout', $registration);
                Mail::to($registration->email)->send(new CoursePaymentRequired($registration, $paymentUrl));
            }

            return back()->with('status', 'Osallistuja lisätty — maksulinkki lähetetty sähköpostiin.');
        }

        // Vaihtoehto 2: maksaa vasta kurssipäivänä. Ei maksulinkkiä, paikka
        // pysyy varattuna kurssin loppuun asti ja merkitään maksetuksi
        // Kurssikortista tai Osallistujakortista.
        if ($paymentChoice === 'pay_on_day') {
            $registration = CourseRegistration::create([
                'company_id' => $course->company_id,
                'course_id' => $course->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => 'pending',
                'payment_choice' => 'pay_on_day',
                'payment_deadline' => $course->starts_at
                    ? $course->starts_at->copy()->endOfDay()
                    : now()->addDays(30),
            ]);

            return back()->with('status', 'Osallistuja lisätty — maksaa kurssipäivänä.');
        }

        // Vaihtoehto 3: maksettu heti, tai maksuton kurssi.
        $registration = CourseRegistration::create([
            'company_id' => $course->company_id,
            'course_id' => $course->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'confirmed',
            'payment_method' => $requiresPayment ? 'manual' : null,
            'paid_at' => $requiresPayment ? now() : null,
        ]);

        if ($registration->email) {
            Mail::to($registration->email)->send(new CourseRegistrationConfirmed($registration));
        }

        return back()->with('status', 'Osallistuja lisätty ja vahvistettu.');
    }
}