<x-app-layout>
    <div class="p-6 max-w-3xl">
        <h1 class="text-xl font-semibold">Laskutus</h1>
        <p class="mt-1 text-sm text-gray-500">Ilmoittautumisten maksutilanne, paikan päällä maksaminen ja kuitit/laskut.</p>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Odottaa maksua</h2>
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

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Maksetut</h2>
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
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä maksettuja ilmoittautumisia.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>