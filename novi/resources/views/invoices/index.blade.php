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
                Tee kuitteja päättyneistä hoitokerroista ja hae vanhoja kuitteja
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
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Odottaa laskutusta
                </h2>

                <div class="mt-4 space-y-2">
                    @forelse ($readyToInvoice as $booking)
                                        <div class="flex items-center justify-between rounded-md border p-3" style="border-color: var(--brand-secondary);">
                            <div>
                                <p class="font-semibold" style="color: var(--brand-text);">
                                    {{ $booking->customer->name ?? 'Tuntematon asiakas' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                 {{ $booking->participants->pluck('name')->join(', ') ?: 'Ei lemmikkejä liitetty' }}   
                                    · {{ $booking->arrival_at?->format('d.m.Y') }} – {{ $booking->pickup_at?->format('d.m.Y') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.invoices.store', $booking) }}">
                                    @csrf
                                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                                        Tee kuitti
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.invoices.skip', $booking) }}" onsubmit="return confirm('Merkitäänkö tämä ilman kuittia? Varaus poistuu listalta.');">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600" title="Ei kuittia">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Ei laskuttamattomia hoitokertoja.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Hae vanhoja kuitteja
                </h2>

                <form method="GET" action="{{ route('admin.invoices.index') }}" class="mt-4 flex gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Asiakkaan nimi, puhelin tai sähköposti"
                        class="w-full rounded-md border-gray-300 shadow-sm"
                    >

                    <button type="submit" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-semibold">
                        Hae
                    </button>
                </form>

                @if ($search !== '' && !$customer)
                    <p class="mt-3 text-sm text-gray-500">
                        Asiakasta ei löytynyt.
                    </p>
                @endif
            </section>

                             <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    {{ $customer ? $customer->name . ' - kuitit' : 'Uusimmat kuitit' }}
                </h2>

                @if ($customer && $customerReadyToInvoice->count() > 0)
                    <div class="mt-4 space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Ei vielä laskutettu</p>

                        @foreach ($customerReadyToInvoice as $booking)
                                                <div class="flex items-center justify-between rounded-md border p-3" style="border-color: var(--brand-secondary);">
                                <div>
                                    <p class="text-sm text-gray-700">
                                      {{ $booking->participants->pluck('name')->join(', ') ?: 'Ei lemmikkejä liitetty' }}  
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $booking->arrival_at?->format('d.m.Y') }} – {{ $booking->pickup_at?->format('d.m.Y') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.invoices.store', $booking) }}">
                                        @csrf
                                        <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                                            Tee kuitti
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.invoices.skip', $booking) }}" onsubmit="return confirm('Merkitäänkö tämä ilman kuittia? Varaus poistuu listalta.');">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600" title="Ei kuittia">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 divide-y">   
                    @forelse ($invoices as $invoice)
                        <div
                            onclick="window.open('{{ route('invoices.show', $invoice) }}', '_blank')"
                            class="cursor-pointer flex items-center justify-between py-3 text-sm transition hover:opacity-75"
                        >
                            <div>
                                <p class="font-medium" style="color: var(--brand-text);">
                                    {{ $invoice->invoice_number }}
                                    @if (!$customer)
                                        <span class="text-gray-500">· {{ $invoice->customer->name ?? 'Tuntematon asiakas' }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $invoice->issued_at?->format('d.m.Y') }}
                                </p>
                            </div>

                            <p class="font-semibold" style="color: var(--brand-text);">
                                {{ number_format((float) $invoice->total_due, 2, ',', ' ') }} €
                            </p>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-gray-500">Ei vielä kuitteja.</p>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>