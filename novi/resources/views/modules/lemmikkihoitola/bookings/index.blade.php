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
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-[240px] flex-1">
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

                        @if ($search !== '')
                            <div
                                onclick="window.location.href='{{ route('admin.bookings.index') }}'"
                                class="mt-3 inline-block cursor-pointer text-sm font-medium"
                                style="color: var(--brand-primary);"
                            >
                                ← Näytä kaikki varaukset
                            </div>
                        @endif
                    </div>

                    <div
                        onclick="window.location.href='{{ route('calendar.index', ['varaa' => 1]) }}'"
                        class="btn-brand flex shrink-0 cursor-pointer items-center gap-2 rounded-md px-6 py-3 text-base font-semibold shadow-md"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Uusi varaus
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                        Käynnissä olevat
                    </h2>

                    <div class="mt-4 space-y-3">
                        @forelse ($activeBookings as $booking)
                            @include('bookings._card', ['booking' => $booking, 'mode' => 'future'])
                        @empty
                            <p class="py-6 text-center text-sm text-gray-500">
                                Ei käynnissä olevia varauksia.
                            </p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                        Varausmaksua ei ole vielä hyväksytty
                    </h2>

                    <div class="mt-4 space-y-3">
                                        @forelse ($awaitingPayment as $booking)
                            @include('bookings._card', ['booking' => $booking, 'mode' => 'future', 'showCancel' => true])
                        @empty
                            <p class="py-6 text-center text-sm text-gray-500">
                                Ei maksua odottavia varauksia.
                            </p>
                        @endforelse
                    </div>
                </section>
            </div>

                        @if ($search !== '')
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <section class="rounded-lg bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                            Tulevat varaukset
                        </h2>

                        <div class="mt-4 space-y-3">
                            @forelse ($upcomingBookings as $booking)
                                @include('bookings._card', ['booking' => $booking, 'mode' => 'future', 'showCancel' => true])
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
                                @include('bookings._card', ['booking' => $booking, 'mode' => 'past'])
                            @empty
                                <p class="py-6 text-center text-sm text-gray-500">
                                    Ei menneitä varauksia.
                                </p>
                            @endforelse
                        </div>
                    </section>
                </div>
            @else
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                        Tulevat varaukset
                    </h2>

                    <div class="mt-4 space-y-3">
                        @forelse ($upcomingBookings as $booking)
                            @include('bookings._card', ['booking' => $booking, 'mode' => 'future', 'showCancel' => true])
                        @empty
                            <p class="py-6 text-center text-sm text-gray-500">
                                Ei tulevia varauksia.
                            </p>
                        @endforelse
                    </div>
                </section>
            @endif

        </div>
    </div>
</x-app-layout>