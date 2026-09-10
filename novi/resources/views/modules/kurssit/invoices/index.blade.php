<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Laskutus
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Ilmoittautumisten maksutilanne, paikan päällä maksaminen ja kuitit/laskut.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <div class="flex gap-8">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Tämä kuukausi</p>
                        <p class="mt-1 text-lg font-semibold">{{ number_format($revenueThisMonth, 2, ',', ' ') }} €</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Kaikki maksetut yhteensä</p>
                        <p class="mt-1 text-lg font-semibold">{{ number_format($revenueAllTime, 2, ',', ' ') }} €</p>
                    </div>
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Hae ilmoittautujaa
                </h2>

                <form method="GET" action="{{ route('kurssit.invoices.index') }}" class="mt-4 flex gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Nimi, puhelin tai sähköposti"
                        class="w-full rounded-md border-gray-300 shadow-sm"
                    >

                    <button type="submit" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-semibold">
                        Hae
                    </button>

                    @if ($search !== '')
                        <a href="{{ route('kurssit.invoices.index') }}" class="shrink-0 rounded-md border px-4 py-2 text-sm font-semibold" style="border-color: var(--brand-secondary); color: var(--brand-text);">
                            Tyhjennä
                        </a>
                    @endif
                </form>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Odottaa maksua
                </h2>
        <div class="mt-3 space-y-2">
            @forelse ($pendingPayment as $registration)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $registration->course->name }} · {{ number_format($registration->course->price, 2, ',', ' ') }} €
                        </p>
                    </div>
                    <form method="POST" action="{{ route('kurssit.invoices.mark-paid', $registration) }}">
                        @csrf
                        <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-medium">
                            Merkitse maksetuksi paikan päällä
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei maksua odottavia ilmoittautumisia.</p>
            @endforelse
        </div>

            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Maksetut
                </h2>

        <div class="mt-3 space-y-2">
            @forelse ($paidRegistrations as $registration)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $registration->course->name }} · {{ number_format($registration->course->price, 2, ',', ' ') }} €
                            · {{ $registration->payment_method === 'manual' ? 'Maksettu paikan päällä' : 'Maksettu Stripellä' }}
                            @if ($registration->invoice_number)
                                · {{ $registration->invoice_number }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="window.open('{{ route('kurssit.invoices.print', $registration) }}', '_blank')"
                            class="text-sm text-gray-600 hover:text-gray-900">Kuitti</button>
                        <button type="button" onclick="window.open('{{ route('kurssit.invoices.print', ['registration' => $registration, 'type' => 'lasku']) }}', '_blank')"
                            class="text-sm text-gray-600 hover:text-gray-900">Lasku</button>
                        <form method="POST" action="{{ route('kurssit.invoices.mark-refunded', $registration) }}"
                            onsubmit="return confirm('Merkitäänkö {{ $registration->name }} maksu palautetuksi?');">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Merkitse palautetuksi</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä maksettuja ilmoittautumisia.</p>
            @endforelse
        </div>

            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Erääntyneet (ei maksettu ajoissa)
                </h2>

        <div class="mt-3 space-y-2">
            @forelse ($overdueRegistrations as $registration)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $registration->course->name }} · {{ number_format($registration->course->price, 2, ',', ' ') }} €
                            · Ilmoittautuminen peruuntui automaattisesti
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei erääntyneitä.</p>
            @endforelse
        </div>

            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Palautetut
                </h2>

        <div class="mt-3 space-y-2">
            @forelse ($refundedRegistrations as $registration)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $registration->course->name }} · {{ number_format($registration->course->price, 2, ',', ' ') }} €
                            · Palautettu {{ $registration->refunded_at->format('d.m.Y') }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei palautuksia.</p>
            @endforelse
        </div>
            </section>

        </div>
    </div>
</x-app-layout>