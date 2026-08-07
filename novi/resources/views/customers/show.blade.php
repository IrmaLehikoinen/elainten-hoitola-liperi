<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                {{ $customer->name }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ collect([$customer->phone, $customer->email])->filter()->join(' · ') ?: 'Ei yhteystietoja' }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Perustiedot --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Perustiedot
                </h2>

                <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Puhelin</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $customer->phone ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Sähköposti</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $customer->email ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Osoite</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $customer->address ?: '—' }}</dd>
                    </div>
                </dl>

                @if ($customer->notes)
                    <div class="mt-4 rounded-md bg-gray-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Muistiinpanot</dt>
                        <dd class="mt-1 text-sm whitespace-pre-line" style="color: var(--brand-text);">{{ $customer->notes }}</dd>
                    </div>
                @endif
            </section>

            {{-- Eläimet --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Eläimet
                </h2>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($customer->pets as $pet)
                        <div class="rounded-lg border p-4" style="border-color: var(--brand-secondary);">
                            @if (Route::has('admin.pets.show'))
                                <a href="{{ route('admin.pets.show', $pet->id) }}" class="font-semibold" style="color: var(--brand-primary);">
                                    {{ $pet->name }}
                                </a>
                            @else
                                <p class="font-semibold" style="color: var(--brand-text);">{{ $pet->name }}</p>
                            @endif
                            <p class="text-sm text-gray-500">{{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Ei vielä eläinkortteja.</p>
                    @endforelse
                </div>
            </section>

            {{-- Tulevat varaukset --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Tulevat varaukset
                </h2>

                <div class="mt-4 divide-y">
                    @forelse ($upcomingBookings as $booking)
                        <div class="py-3 text-sm">
                            <p class="font-medium" style="color: var(--brand-text);">
                                {{ $booking->start_date?->format('d.m.Y') }} – {{ $booking->end_date?->format('d.m.Y') }}
                            </p>
                            <p class="text-gray-500">
                                {{ $booking->participants->pluck('name')->join(', ') ?: 'Ei eläimiä liitetty' }}
                            </p>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-gray-500">Ei tulevia varauksia.</p>
                    @endforelse
                </div>
            </section>

            {{-- Menneet varaukset --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Menneet varaukset
                </h2>

                <div class="mt-4 divide-y">
                    @forelse ($pastBookings as $booking)
                        <div class="py-3 text-sm">
                            <p class="font-medium" style="color: var(--brand-text);">
                                {{ $booking->start_date?->format('d.m.Y') }} – {{ $booking->end_date?->format('d.m.Y') }}
                            </p>
                            <p class="text-gray-500">
                                {{ $booking->participants->pluck('name')->join(', ') ?: 'Ei eläimiä liitetty' }}
                            </p>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-gray-500">Ei menneitä varauksia.</p>
                    @endforelse
                </div>
            </section>

            {{-- Laskut --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Laskut
                </h2>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                                <th class="pb-2 pr-4">Kuitti</th>
                                <th class="pb-2 pr-4">Päivämäärä</th>
                                <th class="pb-2 pr-4">Summa</th>
                                <th class="pb-2">Maksettava</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($customer->invoices as $invoice)
                                <tr>
                                    <td class="py-2 pr-4 font-medium">{{ $invoice->invoice_number }}</td>
                                    <td class="py-2 pr-4">{{ $invoice->issued_at?->format('d.m.Y') ?? '—' }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $invoice->subtotal, 2) }} €</td>
                                    <td class="py-2">{{ number_format((float) $invoice->total_due, 2) }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">Ei vielä laskuja.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>