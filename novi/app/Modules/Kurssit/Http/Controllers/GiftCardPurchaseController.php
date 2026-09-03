<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Kurssit\Models\GiftCard;
use App\Services\StripeCheckoutService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GiftCardPurchaseController extends Controller
{
    public function card(GiftCard $giftCard)
    {
        return view('kurssit::public.gift-card-card', [
            'giftCard' => $giftCard,
        ]);
    }

    public function cardPdf(GiftCard $giftCard)
    {
        $pdf = Pdf::loadView('kurssit::gift-cards.pdf', [
            'giftCard' => $giftCard,
        ])->setPaper('a5');

        return $pdf->download('lahjakortti-'.$giftCard->code.'.pdf');
    }
    public function show()
    {
                 $company = Company::where('industry', 'kurssit')->firstOrFail();   

                return view('kurssit::public.gift-card', [
            'onlineEnabled' => $company->settings['gift_cards_online_enabled'] ?? true,
            'company' => $company,
        ]);
    }

    public function store(Request $request, StripeCheckoutService $checkout)
    {
        $company = Company::where('industry', 'kurssit')->firstOrFail();

        if (! ($company->settings['gift_cards_online_enabled'] ?? true)) {
            return back()->with('purchase_error', 'Lahjakortin ostaminen verkossa ei ole tällä hetkellä käytössä.');
        }

                $validated = $request->validate([
            'amount' => ['required', 'in:20,50'],
            'purchaser_name' => ['required', 'string', 'max:255'],
            'purchaser_email' => ['required', 'email'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_email' => ['nullable', 'email'],
            'message' => ['nullable', 'string', 'max:500'],
            'privacy_consent' => ['required', 'accepted'],
        ]);

        $url = $checkout->createSessionUrl([
            'email' => $validated['purchaser_email'],
            'name' => $validated['purchaser_name'],
            'amount' => (float) $validated['amount'],
            'description' => 'Lahjakortti '.$validated['amount'].' €',
            'metadata' => [
                'gift_card_purchase' => '1',
                'company_id' => $company->id,
                'amount' => $validated['amount'],
                'purchaser_name' => $validated['purchaser_name'],
                'purchaser_email' => $validated['purchaser_email'],
                'recipient_name' => $validated['recipient_name'] ?? '',
                'recipient_email' => $validated['recipient_email'] ?? '',
                'message' => $validated['message'] ?? '',
            ],
            'success_url' => route('kurssit.public.gift-card.success'),
            'cancel_url' => route('kurssit.public.gift-card.cancelled'),
        ]);

        return view('kurssit::public.redirecting', ['url' => $url]);
    }
}