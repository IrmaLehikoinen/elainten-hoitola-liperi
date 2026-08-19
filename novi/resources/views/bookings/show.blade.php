<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                    Varaus – {{ $booking->customer->name ?? 'Tuntematon asiakas' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $booking->participants->pluck('name')->join(', ') ?: 'Ei eläimiä liitetty' }}
                </p>
            </div>

            <div
                onclick="window.location.href='{{ route('admin.bookings.index') }}'"
                class="cursor-pointer text-sm font-medium"
                style="color: var(--brand-primary);"
            >
                ← Takaisin varauksiin
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="rounded-lg bg-white p-6 shadow-sm">
                @php
                    $statusLabels = [
                        'confirmed' => 'Vahvistettu',
                        'pending' => 'Odottaa',
                        'cancelled' => 'Peruttu',
                        'completed' => 'Päättynyt',
                    ];
                @endphp

                <div class="flex items-start justify-between gap-4">
                    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                        Perustiedot
                    </h2>

                    <span class="shrink-0 rounded px-2 py-1 text-xs font-medium text-white" style="background-color: var(--brand-primary);">
                        {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <p>
                        <span class="font-medium" style="color: var(--brand-text);">Asiakas:</span>
                        @if ($booking->customer)
                            <span
                                onclick="window.location.href='{{ route('admin.customers.show', $booking->customer->id) }}'"
                                class="cursor-pointer"
                                style="color: var(--brand-primary);"
                            >{{ $booking->customer->name }} →</span>
                        @else
                            Tuntematon asiakas
                        @endif
                    </p>
                    <p>
                        <span class="font-medium" style="color: var(--brand-text);">Palvelu:</span>
                        {{ (($careTypeLabels[$booking->care_type] ?? null)) ?: ($booking->care_type ? ucfirst(str_replace('_', ' ', $booking->care_type)) : '—') }}
                    </p>
                    <p>
                        <span class="font-medium" style="color: var(--brand-text);">Saapuu:</span>
                        {{ $booking->arrival_at?->format('d.m.Y H:i') ?? '—' }}
                    </p>
                    <p>
                        <span class="font-medium" style="color: var(--brand-text);">Noutaa:</span>
                        {{ $booking->pickup_at?->format('d.m.Y H:i') ?? '—' }}
                    </p>
                </div>

                @if ($booking->notes)
                    <p class="mt-4 rounded-md bg-gray-50 p-3 text-sm text-gray-600">
                        {{ $booking->notes }}
                    </p>
                @endif
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Lemmikit
                </h2>

                <div class="mt-4 divide-y">
                    @forelse ($booking->participants as $p)
                        <div class="flex items-center justify-between py-3 text-sm">
                            <div>
                                <p class="font-medium" style="color: var(--brand-text);">{{ $p->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($p->species) }}</p>
                            </div>

                            @if ($p->pet_id && Route::has('admin.pets.show'))
                                <div
                                    onclick="window.location.href='{{ route('admin.pets.show', $p->pet_id) }}'"
                                    class="cursor-pointer text-sm font-medium"
                                    style="color: var(--brand-primary);"
                                >
                                    Avaa →
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="py-4 text-sm text-gray-500">Ei lemmikkejä liitetty.</p>
                    @endforelse
                </div>
            </section>

            @if ($booking->bookingServices->count() > 0)
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                        Lisäpalvelut
                    </h2>

                    <div class="mt-4 divide-y">
                        @foreach ($booking->bookingServices as $bookingService)
                            <div class="flex items-center justify-between py-2 text-sm">
                                <span style="color: var(--brand-text);">{{ $bookingService->service->name ?? 'Palvelu' }}</span>
                                <span class="font-medium" style="color: var(--brand-text);">
                                    {{ number_format((float) $bookingService->price, 2, ',', ' ') }} €
                                </span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Maksu
                </h2>

                <div class="mt-4 space-y-2 text-sm">
                    <p>
                        <span class="font-medium" style="color: var(--brand-text);">Kokonaissumma:</span>
                        {{ $booking->total_price !== null ? number_format((float) $booking->total_price, 2, ',', ' ') . ' €' : '—' }}
                    </p>

                    <div class="flex items-center gap-2">
                        <span class="font-medium" style="color: var(--brand-text);">Ennakkomaksu:</span>
                        @if ((float) $booking->deposit_amount > 0)
                            <span class="rounded px-2 py-0.5 text-xs font-medium text-white" style="background-color: {{ $booking->deposit_paid_at ? 'var(--brand-primary)' : '#b45309' }};">
                                {{ number_format((float) $booking->deposit_amount, 2, ',', ' ') }} €
                                {{ $booking->deposit_paid_at ? '· Maksettu' : '· Odottaa maksua' }}
                            </span>
                        @else
                            <span class="text-gray-400">Ei käytössä</span>
                        @endif
                    </div>

                    @if ($booking->invoice)
                        <div
                            onclick="window.open('{{ route('invoices.show', $booking->invoice) }}', '_blank')"
                            class="cursor-pointer text-sm font-medium"
                            style="color: var(--brand-primary);"
                        >
                            Avaa kuitti/lasku ({{ $booking->invoice->invoice_number }}) →
                        </div>
                    @endif
                </div>
            </section>

            @if ($booking->status !== 'cancelled')
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-md px-4 py-2 text-sm font-semibold"
                            style="background-color: var(--brand-secondary); color: var(--brand-text);"
                            onclick="return confirm('Peruutetaanko tämä varaus?');"
                        >
                            Peruuta varaus
                        </button>
                    </form>
                </section>
            @endif

        </div>
    </div>
</x-app-layout>