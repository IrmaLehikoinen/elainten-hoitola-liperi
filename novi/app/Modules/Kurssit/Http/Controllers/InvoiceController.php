<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\CourseRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $pendingPayment = CourseRegistration::whereHas('course', fn ($q) => $q->where('price', '>', 0))
            ->where('status', 'pending')
            ->with('course')
            ->orderByDesc('created_at')
            ->get();

        $paidRegistrations = CourseRegistration::whereHas('course', fn ($q) => $q->where('price', '>', 0))
            ->where('status', 'confirmed')
            ->with('course')
            ->orderByDesc('paid_at')
            ->get();

        return view('kurssit::invoices.index', [
            'pendingPayment' => $pendingPayment,
            'paidRegistrations' => $paidRegistrations,
        ]);
    }

    public function markPaid(CourseRegistration $registration)
    {
        $registration->status = 'confirmed';
        $registration->payment_method = 'manual';
        $registration->paid_at = now();
        $registration->save();

        return back()->with('status', 'Merkitty maksetuksi paikan päällä.');
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