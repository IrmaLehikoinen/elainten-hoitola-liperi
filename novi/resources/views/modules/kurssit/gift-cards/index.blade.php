<x-app-layout>
    <div class="p-6 max-w-3xl">
        <h1 class="text-xl font-semibold">Lahjakortit</h1>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 rounded-lg border bg-white p-5 max-w-md">
            <h2 class="text-sm font-semibold text-gray-700">Lahjakorttien asetukset</h2>

            <form method="POST" action="{{ route('kurssit.gift-cards.update-settings') }}" class="mt-3 space-y-3">
                @csrf
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="online_enabled" value="1" {{ $onlineEnabled ? 'checked' : '' }}>
                    Verkkomyynti käytössä
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="in_person_enabled" value="1" {{ $inPersonEnabled ? 'checked' : '' }}>
                    Paikan päällä myynti käytössä
                </label>
                <div>
                    <label class="block text-sm font-medium">Verkosta ostetun kortin voimassaoloaika (kuukausina)</label>
                    <input type="number" name="validity_months" min="1" max="60" value="{{ $validityMonths }}" required class="mt-1 w-32 rounded-md border-gray-300 text-sm">
                </div>
                <button type="submit" class="rounded-md border px-4 py-2 text-sm font-medium">Tallenna asetukset</button>
            </form>
        </div>

        @if ($inPersonEnabled)
            <div class="mt-6 rounded-lg border bg-white p-5 max-w-md">
                <h2 class="text-sm font-semibold text-gray-700">Uusi lahjakortti (paikan päällä myyty)</h2>

                <form method="POST" action="{{ route('kurssit.gift-cards.store') }}" class="mt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Kortin numero</label>
                        <input type="text" name="code" class="mt-1 w-full rounded-md border-gray-300 text-sm" placeholder="esim. S0001 — lukee valmiiksi painetussa kortissa">
                        <p class="mt-1 text-xs text-gray-500">Jätä tyhjäksi jos haluat järjestelmän keksivän numeron.</p>
                    </div>
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
        @else
            <p class="mt-6 text-sm text-gray-500">Paikan päällä myynti on pois käytöstä. Ota se käyttöön yllä olevista asetuksista, jos haluat luoda uusia kortteja.</p>
        @endif

                      <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Myydyt lahjakortit</h2>
                  <div class="mt-3 flex gap-2">
            <button type="button" onclick="window.location.href='{{ route('kurssit.gift-cards.index') }}'"
                class="rounded-md border px-3 py-1 text-xs {{ ! $filter ? 'font-medium' : 'text-gray-600' }}"
                style="{{ ! $filter ? 'background: var(--brand-primary, #3F4F3A); color: white; border-color: var(--brand-primary, #3F4F3A);' : '' }}">Kaikki</button>
            <button type="button" onclick="window.location.href='{{ route('kurssit.gift-cards.index', ['lahde' => 'in_person']) }}'"
                class="rounded-md border px-3 py-1 text-xs {{ $filter === 'in_person' ? 'font-medium' : 'text-gray-600' }}"
                style="{{ $filter === 'in_person' ? 'background: var(--brand-primary, #3F4F3A); color: white; border-color: var(--brand-primary, #3F4F3A);' : '' }}">Paikan päällä myydyt</button>
            <button type="button" onclick="window.location.href='{{ route('kurssit.gift-cards.index', ['lahde' => 'online']) }}'"
                class="rounded-md border px-3 py-1 text-xs {{ $filter === 'online' ? 'font-medium' : 'text-gray-600' }}"
                style="{{ $filter === 'online' ? 'background: var(--brand-primary, #3F4F3A); color: white; border-color: var(--brand-primary, #3F4F3A);' : '' }}">Verkosta ostetut</button>
        </div>  
        <div class="mt-3 space-y-2">  
            @forelse ($giftCards as $card)
                <div class="rounded-lg border bg-white p-4">
                    <div class="flex items-center justify-between">
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
                                                <div class="flex items-center gap-3">
                            @if ((float) $card->balance <= 0)
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">Käytetty loppuun</span>
                            @elseif ($card->isExpired())
                                <span class="rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Vanhentunut</span>
                            @else
                                <span class="rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Aktiivinen</span>
                            @endif
                        </div>
                    </div>

                    @if ($card->registrations->isNotEmpty())
                        <div class="mt-3 border-t pt-2 space-y-1">
                            @foreach ($card->registrations as $usage)
                                <p class="text-xs text-gray-500">
                                    Käytetty: {{ number_format($usage->gift_card_amount, 2, ',', ' ') }} €
                                    — {{ $usage->name }}, {{ $usage->course->name ?? '' }},
                                    {{ $usage->gift_card_applied_at->format('d.m.Y') }}
                                </p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä yhtään lahjakorttia.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>