<x-app-layout>
    <div class="p-6 max-w-3xl">
        <h1 class="text-xl font-semibold">Lahjakortit</h1>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 rounded-lg border bg-white p-5 max-w-md">
            <h2 class="text-sm font-semibold text-gray-700">Uusi lahjakortti (paikan päällä myyty)</h2>

            <form method="POST" action="{{ route('kurssit.gift-cards.store') }}" class="mt-3 space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Summa</label>
                    <select name="initial_amount" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        <option value="20">20 €</option>
                        <option value="50">50 €</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Voimassa asti (valinnainen)</label>
                    <input type="date" name="valid_until" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Ostajan nimi (valinnainen)</label>
                    <input type="text" name="purchaser_name" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                </div>
                <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-medium">Luo lahjakortti</button>
            </form>
        </div>

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Kaikki lahjakortit</h2>
        <div class="mt-3 space-y-2">
            @forelse ($giftCards as $card)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $card->code }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $card->source === 'online' ? 'Verkosta ostettu' : 'Paikan päällä myyty' }}
                            · {{ number_format($card->balance, 2, ',', ' ') }} € / {{ number_format($card->initial_amount, 2, ',', ' ') }} €
                            @if ($card->valid_until)
                                · Voimassa {{ $card->valid_until->format('d.m.Y') }} asti
                            @endif
                            @if ($card->purchaser_name)
                                · {{ $card->purchaser_name }}
                            @endif
                        </p>
                    </div>
                    <div>
                        @if ((float) $card->balance <= 0)
                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">Käytetty loppuun</span>
                        @elseif ($card->isExpired())
                            <span class="rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Vanhentunut</span>
                        @else
                            <span class="rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Aktiivinen</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä yhtään lahjakorttia.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>