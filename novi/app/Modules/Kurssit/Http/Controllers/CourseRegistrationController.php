<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Kurssit\Mail\CourseRegistrationConfirmed;
use App\Modules\Kurssit\Models\Course;
use App\Modules\Kurssit\Models\CourseRegistration;
use App\Modules\Kurssit\Models\GiftCard;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CourseRegistrationController extends Controller
{
    public function index()
    {
$company = Company::where('industry', 'kurssit')->sole();     

                $courses = Course::where('company_id', $company->id)
            ->whereNull('cancelled_at')
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '>=', now());
            })
            ->orderBy('starts_at')
            ->get();

                return view('kurssit::public.index', [
            'courses' => $courses,
            'onlineGiftCardsEnabled' => $company->settings['gift_cards_online_enabled'] ?? true,
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
            'gift_card_code' => ['nullable', 'string', 'max:20'],
            'privacy_consent' => ['required', 'accepted'],
        ]);

        if ($course->isCancelled()) {
            return back()
                ->with('registration_error', 'Tämä kurssi on peruttu.')
                ->withInput();
        }

        if ($course->isRegistrationClosed()) {
            return back()
                ->with('registration_error', 'Ilmoittautuminen tälle kurssille on suljettu.')
                ->withInput();
        }

        if ($course->remainingSpots() <= 0) {
            return back()
                ->with('registration_error', 'Valitettavasti kurssi on juuri täyttynyt.')
                ->withInput();
        }

        // Lahjakortti: jos koodi annettu ja se on käyttökelpoinen, lasketaan
        // kuinka paljon se kattaa kurssin hinnasta. Saldoa EI vielä vähennetä
        // tässä vaiheessa — vasta kun maksu on lopullisesti vahvistettu.
        $giftCard = null;
        $giftCardAmount = null;

        if ($request->filled('gift_card_code')) {
            $giftCard = GiftCard::findUsable($request->input('gift_card_code'));

            if (! $giftCard) {
                return back()
                    ->with('registration_error', 'Lahjakorttia ei löytynyt tai se ei ole enää voimassa.')
                    ->withInput();
            }

            $giftCardAmount = min((float) $giftCard->balance, (float) $course->price);
        }

        $amountDue = max(0, (float) $course->price - (float) ($giftCardAmount ?? 0));
        $requiresPayment = $amountDue > 0;

        $registration = CourseRegistration::create([
            'company_id' => $course->company_id,
            'course_id' => $course->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $requiresPayment ? 'pending' : 'confirmed',
            'payment_deadline' => $requiresPayment ? now()->addMinutes(30) : null,
            'gift_card_id' => $giftCard?->id,
            'gift_card_amount' => $giftCardAmount,
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
            $registration->applyGiftCardIfNeeded();

            Mail::to($registration->email)->send(new CourseRegistrationConfirmed($registration));

            return redirect()->route('kurssit.public.success');
        }

        $url = $checkout->createSessionUrl([
            'email' => $registration->email,
            'name' => $registration->name,
            'amount' => $amountDue,
            'description' => 'Kurssi-ilmoittautuminen: '.$course->name,
            'metadata' => ['course_registration_id' => $registration->id],
            'preferred_deadline' => $registration->payment_deadline,
            'success_url' => route('kurssit.public.success'),
            'cancel_url' => route('kurssit.public.cancelled'),
        ]);

        return view('kurssit::public.redirecting', ['url' => $url]);
    }
}