<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Palvelut
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Valitse asiakas ja merkitse hänelle käytetyt lisäpalvelut
            </p>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ modalOpen: {{ $customer ? 'true' : 'false' }} }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Hae asiakas
                </h2>

                <form method="GET" action="{{ route('admin.services.index') }}" class="mt-4 flex gap-2">
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
                    Talossa juuri nyt
                </h2>

                <div class="mt-4 space-y-2">
                    @forelse ($inHousePets as $participant)
                        <div
                            onclick="window.location.href='{{ route('admin.services.index', ['customer_id' => optional($participant->booking)->customer_id, 'booking_id' => $participant->booking_id]) }}'"
                            class="cursor-pointer flex items-center justify-between rounded-md border p-3 transition hover:shadow-md"
                            style="border-color: var(--brand-secondary);"
                        >
                            <div>
                                <p class="font-semibold" style="color: var(--brand-text);">{{ $participant->name }}</p>
                                <p class="text-xs text-gray-500">{{ optional(optional($participant->booking)->customer)->name ?? 'Tuntematon asiakas' }}</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ optional($participant->booking)->pickup_at?->format('d.m.Y') }} asti
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Ei lemmikkejä hoidossa juuri nyt.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Tulevat lemmikit
                </h2>

                <div class="mt-4 space-y-2">
                    @forelse ($upcomingPets as $participant)
                        <div
                            onclick="window.location.href='{{ route('admin.services.index', ['customer_id' => optional($participant->booking)->customer_id, 'booking_id' => $participant->booking_id]) }}'"
                            class="cursor-pointer flex items-center justify-between rounded-md border p-3 transition hover:shadow-md"
                            style="border-color: var(--brand-secondary);"
                        >
                            <div>
                                <p class="font-semibold" style="color: var(--brand-text);">{{ $participant->name }}</p>
                                <p class="text-xs text-gray-500">{{ optional(optional($participant->booking)->customer)->name ?? 'Tuntematon asiakas' }}</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ optional($participant->booking)->arrival_at?->format('d.m.Y') }} alkaen
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Ei tulevia lemmikkejä.</p>
                    @endforelse
                </div>
            </section>

            @if ($customer)
                <div
                    x-show="modalOpen"
                    x-cloak
                    class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 p-4"
                    @keydown.escape.window="modalOpen = false"
                >
                <section
                    class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-xl"
                    @click.outside="modalOpen = false"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xl font-semibold" style="color: var(--brand-text);">
                                {{ $customer->name }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ collect([$customer->phone, $customer->email])->filter()->join(' · ') ?: 'Ei yhteystietoja' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <div
                                onclick="window.location.href='{{ route('admin.customers.show', $customer->id) }}'"
                                class="cursor-pointer text-sm font-medium"
                                style="color: var(--brand-primary);"
                            >
                                Avaa asiakaskortti →
                            </div>

                            <button
                                type="button"
                                class="rounded-md p-1 text-gray-500 hover:bg-gray-100"
                                @click="modalOpen = false"
                                aria-label="Sulje"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    @if ($bookings->isEmpty())
                        <p class="mt-4 text-sm text-gray-500">
                            Ei käynnissä olevaa tai tulevaa varausta.
                        </p>
                    @else
                        @if ($bookings->count() > 1)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($bookings as $booking)
                                    <div
                                        onclick="window.location.href='{{ route('admin.services.index', ['customer_id' => $customer->id, 'booking_id' => $booking->id]) }}'"
                                        class="cursor-pointer rounded-md px-3 py-2 text-sm font-medium"
                                        style="
                                            {{ $selectedBooking && $selectedBooking->id === $booking->id ? 'background-color: var(--brand-secondary);' : 'border: 1px solid var(--brand-secondary);' }}
                                            color: var(--brand-text);
                                        "
                                    >
                                        {{ $booking->arrival_at?->format('d.m.Y') }} – {{ $booking->pickup_at?->format('d.m.Y') }}
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($selectedBooking)
                            <form method="POST" action="{{ route('admin.services.update', $selectedBooking) }}" class="mt-6">
                                @csrf

                                <p class="text-sm text-gray-500">
                                    Varaus: {{ $selectedBooking->arrival_at?->format('d.m.Y') }} – {{ $selectedBooking->pickup_at?->format('d.m.Y') }}
                                </p>

                                <div class="mt-4 space-y-2">
                                    @forelse ($services as $service)
                                        <label class="flex items-center justify-between rounded-md border p-3" style="border-color: var(--brand-secondary);">
                                            <span class="flex items-center gap-3">
                                                <input
                                                    type="checkbox"
                                                    name="service_ids[]"
                                                    value="{{ $service->id }}"
                                                    @checked(in_array($service->id, $selectedServiceIds))
                                                    class="rounded border-gray-300"
                                                >
                                                <span>
                                                    <span class="font-medium" style="color: var(--brand-text);">{{ $service->name }}</span>
                                                    @if ($service->description)
                                                        <span class="block text-xs text-gray-500">{{ $service->description }}</span>
                                                    @endif
                                                </span>
                                            </span>

                                            <span class="text-sm font-medium" style="color: var(--brand-text);">
                                                {{ number_format((float) $service->price, 2, ',', ' ') }} €
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500">
                                            Ei lisäpalveluita määritelty. Lisää niitä Asetukset → Lisäpalvelut.
                                        </p>
                                    @endforelse
                                </div>

                                @if ($services->isNotEmpty())
                                    <button type="submit" class="btn-brand mt-4 rounded-md px-4 py-2 text-sm font-semibold">
                                        Tallenna palvelut
                                    </button>
                                @endif
                            </form>
                        @endif
                    @endif
                </section>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>