<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Varaukset
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kaikki tehdyt varaukset
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Hae varausta
                </h2>

                <form method="GET" action="{{ route('admin.bookings.index') }}" class="mt-4 flex gap-2">
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
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Tulevat ja käynnissä olevat
                </h2>

                <div class="mt-4 space-y-3">
                    @forelse ($upcomingBookings as $booking)
                        @php
                            $statusLabels = [
                                'confirmed' => 'Vahvistettu',
                                'pending' => 'Odottaa',
                                'cancelled' => 'Peruttu',
                                'completed' => 'Päättynyt',
                            ];
                        @endphp

                        <div
                            @if ($booking->customer) onclick="window.location.href='{{ route('admin.customers.show', $booking->customer->id) }}'" @endif
                            class="cursor-pointer rounded-lg border p-4 transition hover:shadow-md"
                            style="border-color: var(--brand-secondary);"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="font-semibold" style="color: var(--brand-text);">
                                        {{ $booking->customer->name ?? 'Tuntematon asiakas' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $booking->participants->pluck('name')->join(', ') }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded px-2 py-1 text-xs font-medium text-white" style="background-color: var(--brand-primary);">
                                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-600 sm:grid-cols-2">
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
                                <p class="mt-3 rounded-md bg-gray-50 p-3 text-sm text-gray-600">
                                    {{ $booking->notes }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">
                            Ei tulevia varauksia.
                        </p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Menneet
                </h2>

                <div class="mt-4 space-y-3">
                    @forelse ($pastBookings as $booking)
                        @php
                            $statusLabels = [
                                'confirmed' => 'Vahvistettu',
                                'pending' => 'Odottaa',
                                'cancelled' => 'Peruttu',
                                'completed' => 'Päättynyt',
                            ];
                        @endphp

                        <div
                            @if ($booking->customer) onclick="window.location.href='{{ route('admin.customers.show', $booking->customer->id) }}'" @endif
                            class="cursor-pointer rounded-lg border p-4 opacity-75 transition hover:opacity-100 hover:shadow-md"
                            style="border-color: var(--brand-secondary);"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="font-semibold" style="color: var(--brand-text);">
                                        {{ $booking->customer->name ?? 'Tuntematon asiakas' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $booking->participants->pluck('name')->join(', ') }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded px-2 py-1 text-xs font-medium" style="background-color: var(--brand-secondary); color: var(--brand-text);">
                                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-600 sm:grid-cols-2">
                                <p>
                                    <span class="font-medium" style="color: var(--brand-text);">Saapui:</span>
                                    {{ $booking->arrival_at?->format('d.m.Y H:i') ?? '—' }}
                                </p>
                                <p>
                                    <span class="font-medium" style="color: var(--brand-text);">Noudettu:</span>
                                    {{ $booking->pickup_at?->format('d.m.Y H:i') ?? '—' }}
                                </p>
                            </div>

                            @if ($booking->notes)
                                <p class="mt-3 rounded-md bg-gray-50 p-3 text-sm text-gray-600">
                                    {{ $booking->notes }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">
                            Ei menneitä varauksia.
                        </p>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>