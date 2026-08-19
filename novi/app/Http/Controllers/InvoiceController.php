<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $readyToInvoice = Booking::with(['customer', 'participants'])
            ->whereNotNull('end_date')
            ->where('end_date', '<', today())
            ->whereDoesntHave('invoice')
            ->orderByDesc('end_date')
            ->get();

        $search = trim((string) $request->get('q'));
        $customer = null;
        $invoices = collect();

        if ($search !== '') {
            $digitsOnly = preg_replace('/[\s\-]+/', '', $search);

            $customer = Customer::where(function ($builder) use ($search, $digitsOnly) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if ($digitsOnly !== '') {
                        $builder->orWhereRaw(
                            "REPLACE(REPLACE(phone, ' ', ''), '-', '') LIKE ?",
                            ["%{$digitsOnly}%"]
                        );
                    }
                })
                ->first();

                        if ($customer) {
                $invoices = Invoice::where('customer_id', $customer->id)
                    ->orderByDesc('issued_at')
                    ->get();
            }
        } else {
            $invoices = Invoice::with('customer')
                ->orderByDesc('issued_at')
                ->limit(20)
                ->get();
        }

        $customerReadyToInvoice = $customer
            ? $readyToInvoice->where('customer_id', $customer->id)->values()
            : collect();

        return view('invoices.index', [
            'readyToInvoice' => $readyToInvoice,
            'search' => $search,
            'customer' => $customer,
            'invoices' => $invoices,
            'customerReadyToInvoice' => $customerReadyToInvoice,
        ]);
    }

    public function store(Booking $booking)
    {
        if ($booking->invoice) {
            return redirect()
                ->route('invoices.show', $booking->invoice)
                ->with('status', 'Tälle hoitokerralle on jo tehty kuitti.');
        }

        $lineItems = [];
        $subtotal = 0;

        foreach ($booking->participants as $participant) {
            $days = $participant->careDays();
            $rowSubtotal = $participant->careSubtotal();

            $lineItems[] = [
                'type' => 'care',
                'label' => $participant->name . ' (' . $participant->species . ')',
                'days' => $days,
                'rate' => (float) $participant->daily_rate,
                'subtotal' => $rowSubtotal,
            ];

            $subtotal += $rowSubtotal;
        }

        foreach ($booking->bookingServices as $bookingService) {
            $lineItems[] = [
                'type' => 'service',
                'label' => $bookingService->service->name ?? 'Lisäpalvelu',
                'days' => null,
                'rate' => null,
                'subtotal' => (float) $bookingService->price,
            ];

            $subtotal += (float) $bookingService->price;
        }

        $depositAmount = (float) ($booking->deposit_amount ?? 0);
        $totalDue = max(0, $subtotal - $depositAmount);

        $company = $booking->company;
        $vatPercentage = (float) ($company->settings['vat_percentage'] ?? 25.5);
        $vatAmount = round($subtotal - ($subtotal / (1 + $vatPercentage / 100)), 2);

        $year = now()->format('Y');
        $lastNumber = Invoice::where('invoice_number', 'like', "KUITTI-{$year}-%")
            ->orderByDesc('invoice_number')
            ->value('invoice_number');
        $nextSequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        $invoiceNumber = 'KUITTI-' . $year . '-' . str_pad(
            (string) $nextSequence,
            4,
            '0',
            STR_PAD_LEFT
        );

        $invoice = Invoice::create([
            'company_id' => $booking->company_id,
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'invoice_number' => $invoiceNumber,
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'vat_percentage' => $vatPercentage,
            'vat_amount' => $vatAmount,
            'deposit_amount' => $depositAmount,
            'total_due' => $totalDue,
            'issued_at' => now(),
        ]);

        return redirect()->route('invoices.show', $invoice);
    }

   public function show(Invoice $invoice)
    {
        $invoice->load(['booking.participants', 'customer', 'company']);

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }

public function downloadPdf(Invoice $invoice, Request $request)
    {
        $invoice->load(['booking.participants', 'customer', 'company']);
        $isInvoice = $request->query('type') === 'lasku';

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'isInvoice' => $isInvoice,
        ]);

        $fileName = ($isInvoice ? 'lasku-' : 'kuitti-') . $invoice->invoice_number . '.pdf';

        return $pdf->download($fileName);
    }

    public function printPdf(Invoice $invoice, Request $request)
    {
        $invoice->load(['booking.participants', 'customer', 'company']);
        $isInvoice = $request->query('type') === 'lasku';

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'isInvoice' => $isInvoice,
        ]);

        $fileName = ($isInvoice ? 'lasku-' : 'kuitti-') . $invoice->invoice_number . '.pdf';

        return $pdf->stream($fileName);
    }
}