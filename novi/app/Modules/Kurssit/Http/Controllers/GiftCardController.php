<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\GiftCard;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function index()
    {
        return view('kurssit::gift-cards.index', [
            'giftCards' => GiftCard::orderByDesc('created_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'initial_amount' => ['required', 'in:20,50'],
            'valid_until' => ['nullable', 'date'],
            'purchaser_name' => ['nullable', 'string', 'max:255'],
        ]);

        GiftCard::create([
            'code' => GiftCard::generateCode('in_person'),
            'source' => 'in_person',
            'initial_amount' => $validated['initial_amount'],
            'balance' => $validated['initial_amount'],
            'valid_until' => $validated['valid_until'] ?? null,
            'purchaser_name' => $validated['purchaser_name'] ?? null,
        ]);

        return back()->with('status', 'Lahjakortti luotu.');
    }
}