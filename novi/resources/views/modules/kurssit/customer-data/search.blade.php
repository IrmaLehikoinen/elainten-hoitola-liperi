<x-app-layout>
    <div class="p-6 max-w-3xl">
        <h1 class="text-xl font-semibold">Asiakastietojen haku (tietopyyntö)</h1>
        <p class="mt-1 text-sm text-gray-500">Hae kaikki tietyn nimen, sähköpostiosoitteen tai puhelinnumeron alla olevat kurssi-ilmoittautumiset ja lahjakortit, esimerkiksi asiakkaan tietopyynnön yhteydessä.</p>

        <form method="GET" action="{{ route('kurssit.customer-data.index') }}" class="mt-6 flex gap-2 max-w-md no-print">
            <input type="text" name="haku" value="{{ $search }}" placeholder="Nimi, sähköposti tai puhelinnumero" class="flex-1 rounded-md border-gray-300 text-sm">
            <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-medium">Hae</button>
        </form>

        @if ($searched)
            @if ($registrations->isEmpty() && $giftCards->isEmpty())
                <p class="mt-6 text-sm text-gray-500">Ei löytynyt tietoja haulla "{{ $search }}".</p>
            @else
                <div class="mt-8">
                    <button type="button" onclick="window.print()" class="no-print rounded-md border px-4 py-2 text-sm font-medium">Tulosta</button>

                    <h2 class="mt-6 text-lg font-semibold">Tiedot haulla: {{ $search }}</h2>

                    @if ($registrations->isNotEmpty())
                        <h3 class="mt-6 text-sm font-semibold text-gray-500 uppercase tracking-wide">Kurssi-ilmoittautumiset</h3>
                        <div class="mt-2 space-y-2">
                            @foreach ($registrations as $reg)
                                <div class="rounded-lg border bg-white p-4 text-sm">
                                    <p class="font-medium">{{ $reg->course->name ?? 'Kurssi poistettu' }}</p>
                                    <p class="text-gray-500">Nimi: {{ $reg->name }} · Sähköposti: {{ $reg->email }} · Puhelin: {{ $reg->phone ?? '—' }}</p>
                                    <p class="text-gray-500">Tila: {{ $reg->status }} · Ilmoittautunut: {{ $reg->created_at->format('d.m.Y') }}</p>
                                    @if ($reg->paid_at)
                                        <p class="text-gray-500">Maksettu: {{ $reg->paid_at->format('d.m.Y') }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($giftCards->isNotEmpty())
                        <h3 class="mt-6 text-sm font-semibold text-gray-500 uppercase tracking-wide">Lahjakortit</h3>
                        <div class="mt-2 space-y-2">
                            @foreach ($giftCards as $card)
                                <div class="rounded-lg border bg-white p-4 text-sm">
                                    <p class="font-medium">{{ $card->code }}</p>
                                    <p class="text-gray-500">Ostaja: {{ $card->purchaser_name }} · {{ $card->purchaser_email }}</p>
                                    <p class="text-gray-500">Summa: {{ number_format($card->initial_amount, 2, ',', ' ') }} € · Saldo: {{ number_format($card->balance, 2, ',', ' ') }} €</p>
                                    <p class="text-gray-500">Ostettu: {{ $card->created_at->format('d.m.Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</x-app-layout>