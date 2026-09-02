<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\GiftCard;
use App\Services\ActiveCompanyResolver;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function index(Request $request)
    {
        $company = app(ActiveCompanyResolver::class)->current();
        $filter = $request->query('lahde');

        $giftCards = GiftCard::with(['registrations' => function ($query) {
                $query->whereNotNull('gift_card_applied_at')->with('course');
            }])
            ->when($filter === 'online', fn ($query) => $query->where('source', 'online'))
            ->when($filter === 'in_person', fn ($query) => $query->where('source', 'in_person'))
            ->orderByDesc('created_at')
            ->get();

        return view('kurssit::gift-cards.index', [
            'giftCards' => $giftCards,
            'onlineEnabled' => $company->settings['gift_cards_online_enabled'] ?? true,
            'inPersonEnabled' => $company->settings['gift_cards_in_person_enabled'] ?? true,
            'validityMonths' => $company->settings['gift_cards_validity_months'] ?? 12,
            'filter' => $filter,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:20', 'unique:gift_cards,code'],
            'initial_amount' => ['required', 'in:20,50'],
            'valid_until' => ['nullable', 'date'],
            'purchaser_name' => ['nullable', 'string', 'max:255'],
        ]);

        GiftCard::create([
            'code' => $validated['code']
                ? strtoupper(trim($validated['code']))
                : GiftCard::generateCode('in_person'),
            'source' => 'in_person',
            'initial_amount' => $validated['initial_amount'],
            'balance' => $validated['initial_amount'],
            'valid_until' => $validated['valid_until'] ?? null,
            'purchaser_name' => $validated['purchaser_name'] ?? null,
        ]);

        return back()->with('status', 'Lahjakortti luotu.');
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'validity_months' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $company = app(ActiveCompanyResolver::class)->current();

        $settings = $company->settings ?? [];
        $settings['gift_cards_online_enabled'] = $request->boolean('online_enabled');
        $settings['gift_cards_in_person_enabled'] = $request->boolean('in_person_enabled');
                $settings['gift_cards_validity_months'] = (int) $validated['validity_months'];

        $company->settings = $settings;
        $company->save();

        return back()->with('status', 'Lahjakorttien asetukset päivitetty.');
    }
}