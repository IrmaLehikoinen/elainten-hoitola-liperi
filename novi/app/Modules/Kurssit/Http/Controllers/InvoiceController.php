<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Mail\CourseRegistrationConfirmed;
use App\Modules\Kurssit\Models\CourseRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        $applySearch = function ($query) use ($search) {
            if ($search === '') {
                return;
            }

            $digitsOnly = preg_replace('/[\s\-]+/', '', $search);

            $query->where(function ($builder) use ($search, $digitsOnly) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");

                if ($digitsOnly !== '') {
                    $builder->orWhereRaw(
                        "REPLACE(REPLACE(phone, ' ', ''), '-', '') LIKE ?",
                        ["%{$digitsOnly}%"]
                    );
                }
            });
        };

        $pendingPayment = tap(
            CourseRegistration::whereHas('course', fn ($q) => $q->where('price', '>', 0))
                ->where('status', 'pending'),
            $applySearch
        )
            ->with('course')
            ->orderByDesc('created_at')
            ->get();

        $paidRegistrations = tap(
            CourseRegistration::whereHas('course', fn ($q) => $q->where('price', '>', 0))
                ->where('status', 'confirmed')
                ->whereNull('refunded_at'),
            $applySearch
        )
            ->with('course')
            ->orderByDesc('paid_at')
            ->get();

        $overdueRegistrations = tap(
            CourseRegistration::whereHas('course', fn ($q) => $q->where('price', '>', 0))
                ->where('status', 'cancelled')
                ->where('cancellation_reason', 'payment_expired'),
            $applySearch
        )
            ->with('course')
            ->orderByDesc('updated_at')
            ->get();

        $refundedRegistrations = tap(
            CourseRegistration::whereHas('course', fn ($q) => $q->where('price', '>', 0))
                ->whereNotNull('refunded_at'),
            $applySearch
        )
            ->with('course')
            ->orderByDesc('refunded_at')
            ->get();

        $paidThisMonth = CourseRegistration::whereHas('course', fn ($q) => $q->where('price', '>', 0))
            ->where('status', 'confirmed')
            ->whereNull('refunded_at')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->with('course')
            ->get();

        $revenueThisMonth = $paidThisMonth->sum(fn ($r) => (float) $r->course->price);
        $revenueAllTime = $paidRegistrations->sum(fn ($r) => (float) $r->course->price);

        return view('kurssit::invoices.index', [
            'pendingPayment' => $pendingPayment,
            'paidRegistrations' => $paidRegistrations,
            'overdueRegistrations' => $overdueRegistrations,
            'refundedRegistrations' => $refundedRegistrations,
            'revenueThisMonth' => $revenueThisMonth,
            'revenueAllTime' => $revenueAllTime,
            'search' => $search,
        ]);
    }

    public function markPaid(CourseRegistration $registration)
    {
        $wasPending = $registration->status !== 'confirmed';

        $registration->status = 'confirmed';
        $registration->payment_method = 'manual';
        $registration->paid_at = now();
        $registration->save();
        $registration->applyGiftCardIfNeeded();

        if ($wasPending && $registration->email) {
            Mail::to($registration->email)->send(new CourseRegistrationConfirmed($registration));
        }

        return back()->with('status', 'Merkitty maksetuksi paikan päällä.');
    }

    public function markRefunded(CourseRegistration $registration)
    {
        $registration->update(['refunded_at' => now()]);

        return back()->with('status', 'Merkitty palautetuksi.');
    }

    public function downloadPdf(CourseRegistration $registration, Request $request)
    {
        $this->ensureInvoiceNumber($registration);

        $isInvoice = $request->query('type') === 'lasku';

        $pdf = Pdf::loadView('kurssit::invoices.pdf', [
            'registration' => $registration,
            'isInvoice' => $isInvoice,
        ]);

        $fileName = ($isInvoice ? 'lasku-' : 'kuitti-').$registration->invoice_number.'.pdf';

        return $pdf->download($fileName);
    }

    public function printPdf(CourseRegistration $registration, Request $request)
    {
        $this->ensureInvoiceNumber($registration);

        $isInvoice = $request->query('type') === 'lasku';

        $pdf = Pdf::loadView('kurssit::invoices.pdf', [
            'registration' => $registration,
            'isInvoice' => $isInvoice,
        ]);

        $fileName = ($isInvoice ? 'lasku-' : 'kuitti-').$registration->invoice_number.'.pdf';

        return $pdf->stream($fileName);
    }

    private function ensureInvoiceNumber(CourseRegistration $registration): void
    {
        if ($registration->invoice_number) {
            return;
        }

        $year = now()->format('Y');
        $lastNumber = CourseRegistration::where('invoice_number', 'like', "KURSSI-{$year}-%")
            ->orderByDesc('invoice_number')
            ->value('invoice_number');
        $nextSequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        $registration->invoice_number = 'KURSSI-'.$year.'-'.str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
        $registration->issued_at = now();
        $registration->save();
    }
}