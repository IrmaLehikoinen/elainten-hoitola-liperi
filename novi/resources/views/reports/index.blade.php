<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Raportit
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Asiakkaan koko hoitohistoria
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Hae asiakas
                </h2>

                <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-4 flex gap-2">
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

            @if ($customer)
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xl font-semibold" style="color: var(--brand-text);">
                                {{ $customer->name }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ collect([$customer->phone, $customer->email])->filter()->join(' · ') ?: 'Ei yhteystietoja' }}
                            </p>
                        </div>

                        <div
                            onclick="window.location.href='{{ route('admin.customers.show', $customer->id) }}'"
                            class="cursor-pointer text-sm font-medium shrink-0"
                            style="color: var(--brand-primary);"
                        >
                            Avaa asiakaskortti →
                        </div>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                        Hoitokerrat
                    </h2>

                    <div class="mt-4 space-y-2">
                        @forelse ($visits as $visit)
                            @php
                                $statusLabels = [
                                    'confirmed' => 'Vahvistettu',
                                    'pending' => 'Odottaa',
                                    'cancelled' => 'Peruttu',
                                    'completed' => 'Päättynyt',
                                ];
                            @endphp

                            <div x-data="{ open: false }" class="rounded-md border" style="border-color: var(--brand-secondary);">
                                <div
                                    @click="open = !open"
                                    class="cursor-pointer flex items-center justify-between p-3"
                                >
                                    <div>
                                        <p class="font-semibold" style="color: var(--brand-text);">
                                            {{ $visit->arrival_at?->format('d.m.Y') }} – {{ $visit->pickup_at?->format('d.m.Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $visit->participants->pluck('name')->join(', ') ?: 'Ei eläimiä liitetty' }}
                                        </p>
                                    </div>

                                                                    <div class="flex items-center gap-3">
                                        <form method="POST" action="{{ route('admin.invoices.store', $visit) }}" target="_blank" onclick="event.stopPropagation()">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="rounded px-2 py-1 text-xs font-medium"
                                                style="border: 1px solid var(--brand-secondary); color: var(--brand-text);"
                                            >
                                                Tulosta kuitti
                                            </button>
                                        </form>

                                        <span class="rounded px-2 py-0.5 text-xs font-medium" style="background-color: var(--brand-secondary); color: var(--brand-text);">
                                            {{ $statusLabels[$visit->status] ?? ucfirst($visit->status) }}
                                        </span>
                                        <span x-text="open ? '▲' : '▼'" class="text-xs text-gray-400"></span>
                                    </div>
                                </div>

                                <div x-show="open" x-cloak class="border-t p-3 text-sm" style="border-color: var(--brand-secondary);">
                                    <p class="text-gray-500">
                                        Kesto: {{ $visit->nights ?? '—' }} {{ $visit->nights === 1 ? 'vrk' : 'vrk' }}
                                    </p>

                                    @if ($visit->notes)
                                        <div class="mt-2">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Huomiot</p>
                                            <p class="mt-1 rounded-md bg-gray-50 p-3" style="color: var(--brand-text);">
                                                {{ $visit->notes }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="mt-3">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Lisäpalvelut</p>

                                        @if ($visit->bookingServices->isEmpty())
                                            <p class="mt-1 text-gray-500">Ei lisäpalveluita.</p>
                                        @else
                                            <ul class="mt-1 space-y-1">
                                                @foreach ($visit->bookingServices as $bookingService)
                                                    <li class="flex items-center justify-between">
                                                        <span style="color: var(--brand-text);">{{ $bookingService->service->name ?? 'Poistettu palvelu' }}</span>
                                                        <span class="text-gray-500">{{ number_format((float) $bookingService->price, 2, ',', ' ') }} €</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <p class="mt-2 flex items-center justify-between border-t pt-2 font-semibold" style="border-color: var(--brand-secondary); color: var(--brand-text);">
                                                <span>Palvelut yhteensä</span>
                                                <span>{{ number_format((float) $visit->servicesTotal, 2, ',', ' ') }} €</span>
                                            </p>
                                        @endif
                                    </div>

                                    @if ($visit->total_price)
                                        <p class="mt-3 flex items-center justify-between border-t pt-2 font-semibold" style="border-color: var(--brand-secondary); color: var(--brand-text);">
                                            <span>Hoidon kokonaishinta</span>
                                            <span>{{ number_format((float) $visit->total_price, 2, ',', ' ') }} €</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Ei vielä yhtään hoitokertaa.</p>
                        @endforelse
                    </div>
                </section>
            @endif

        </div>
    </div>
</x-app-layout>