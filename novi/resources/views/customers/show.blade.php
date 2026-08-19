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

                @if (request('fromBooking'))
                    <div class="mt-4 rounded-md p-4" style="background-color: var(--brand-secondary);">
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                onclick="handleBackToBooking()"
                                class="btn-brand rounded-md px-4 py-2 text-sm font-semibold"
                            >
                                ← Takaisin ajanvaraukseen (sulje tämä välilehti)
                            </button>

                            <button
                                type="button"
                                onclick="forceCloseBookingTab()"
                                class="rounded-md border px-4 py-2 text-sm font-semibold"
                                style="border-color: var(--brand-primary); color: var(--brand-primary);"
                            >
                                Peruuta ja sulje (älä tallenna)
                            </button>
                        </div>
                    </div>
                @endif

                @if (request('from') === 'bookings')
                    <div class="mt-4 rounded-md p-4" style="background-color: var(--brand-secondary);">
                        <div
                            onclick="window.location.href='{{ route('admin.bookings.index') }}'"
                            class="btn-brand inline-block cursor-pointer rounded-md px-4 py-2 text-sm font-semibold"
                        >
                            ← Takaisin varauksiin
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mt-4 rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="mt-4 space-y-4" id="customer-update-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="from_booking" value="{{ request('fromBooking') ? '1' : '' }}">
                    <input type="hidden" name="species" value="{{ request('species') }}">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Puhelin</label>
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Sähköposti</label>
                            <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Osoite</label>
                            <input type="text" name="address" value="{{ old('address', $customer->address) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Muistiinpanot</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $customer->notes) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Oma hoitopäivähinta (valinnainen)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="custom_daily_rate"
                            value="{{ old('custom_daily_rate', $customer->custom_daily_rate) }}"
                            placeholder="Tyhjä = käytetään yleistä perushintaa"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                        Tallenna tiedot
                    </button>
                </form>
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
                        @if (Route::has('admin.pets.show'))
                                                        <a href="{{ route('admin.pets.show', $pet->id) }}?from=customer&customer={{ $customer->id }}" class="block rounded-lg border p-4 transition hover:shadow-md" style="border-color: var(--brand-secondary);">
                                <p class="font-semibold" style="color: var(--brand-primary);">{{ $pet->name }}</p>
                                <p class="text-sm text-gray-500">{{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
                            </a>
                        @else
                            <div class="rounded-lg border p-4" style="border-color: var(--brand-secondary);">
                                <p class="font-semibold" style="color: var(--brand-text);">{{ $pet->name }}</p>
                                <p class="text-sm text-gray-500">{{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
                            </div>
                        @endif
                    @empty
                        @if ($pendingSpecies->isEmpty())
                            <p class="text-sm text-gray-500">Ei vielä eläinkortteja.</p>
                        @endif
                    @endforelse
                                     @foreach ($pendingSpecies as $species)
                        <div
                            onclick="createAndOpenNewPetFor('{{ $species }}')"
                            class="cursor-pointer rounded-lg border border-dashed p-4 transition hover:shadow-md"
                            style="border-color: var(--brand-primary);"
                        >
                            <p class="font-semibold" style="color: var(--brand-primary);">+ Uusi lemmikki</p>
                            <p class="text-sm text-gray-500">{{ $species }}</p>
                        </div>
                    @endforeach

                    <div
                        onclick="promptNewPet()"
                        class="cursor-pointer rounded-lg border border-dashed p-4 transition hover:shadow-md"
                        style="border-color: var(--brand-primary);"
                    >
                        <p class="font-semibold" style="color: var(--brand-primary);">+ Lisää uusi lemmikki</p>
                        <p class="text-sm text-gray-500">Koira, kissa, kani, muu…</p>
                    </div>
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

            {{-- Valitut palvelut --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <div class="flex items-center justify-between gap-4">
                    <h2
                        class="text-xl font-semibold"
                        style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                    >
                        Valitut palvelut
                    </h2>

                    @if ($activeBooking)
                        <div
                            onclick="window.location.href='{{ route('admin.services.index', ['customer_id' => $customer->id, 'booking_id' => $activeBooking->id]) }}'"
                            class="cursor-pointer text-sm font-medium shrink-0"
                            style="color: var(--brand-primary);"
                        >
                            Muokkaa palveluita →
                        </div>
                    @endif
                </div>

                @if ($activeBooking)
                    <p class="mt-2 text-sm text-gray-500">
                        Lisäpalvelut yhteensä (varaus {{ $activeBooking->arrival_at?->format('d.m.Y') }} – {{ $activeBooking->pickup_at?->format('d.m.Y') }}):
                        <span class="font-semibold" style="color: var(--brand-text);">
                            {{ number_format((float) $selectedServicesTotal, 2, ',', ' ') }} €
                        </span>
                    </p>
                @else
                    <p class="mt-2 text-sm text-gray-500">
                        Ei käynnissä olevaa tai tulevaa varausta, jolle palveluita voisi lisätä.
                    </p>
                @endif
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

<script>
        (function () {
            var form = document.getElementById('customer-update-form');
            var formDirty = false;
            var pendingSpeciesCount = {{ $pendingSpecies->count() }};
            var customerId = {{ $customer->id }};
            var petsStoreUrl = @json(route('admin.pets.store'));
            var petsUpdateUrlBase = @json(url('/admin/pets'));
            var csrfToken = @json(csrf_token());

            if (form) {
                form.addEventListener('input', function () {
                    formDirty = true;
                });

                form.addEventListener('change', function () {
                    formDirty = true;
                });

                form.addEventListener('submit', function () {
                    formDirty = false;
                });
            }

            window.createAndOpenNewPetFor = function (species) {
                fetch(petsStoreUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        customer_id: customerId,
                        name: '',
                        species: species,
                    }),
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (createdPet) {
                        if (!createdPet || !createdPet.id) {
                            alert('Lemmikin luonti epäonnistui.');
                            return;
                        }

                        window.open(petsUpdateUrlBase + '/' + createdPet.id + '?fromBooking=1', '_blank');
                    })
                    .catch(function () {
                        alert('Lemmikin luonti epäonnistui.');
                    });
            };
                        window.promptNewPet = function () {
                var species = window.prompt('Minkä lajin lemmikki? (esim. koira, kissa, kani)');

                if (!species) {
                    return;
                }

                window.createAndOpenNewPetFor(species.trim());
            };

            window.handleBackToBooking = function () {   
                if (formDirty) {
                    alert('Tallenna muutokset ensin ennen kuin palaat varaukseen.');
                    return;
                }

                if (pendingSpeciesCount > 0) {
                    alert('Täytä ensin kaikki lemmikkikortit ennen kuin palaat varaukseen.');
                    return;
                }

               if (window.opener && !window.opener.closed && typeof window.opener.refreshBookingCustomer === 'function') {
                    window.opener.refreshBookingCustomer();
                }

                window.close();
            };

            window.forceCloseBookingTab = function () {
                if (window.opener && !window.opener.closed && typeof window.opener.refreshBookingCustomer === 'function') {
                    window.opener.refreshBookingCustomer();
                }

                window.close();
            };
        })(); 
    </script>    
</x-app-layout>