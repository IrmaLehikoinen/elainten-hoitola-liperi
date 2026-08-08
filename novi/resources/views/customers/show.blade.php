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

                @if (session('status'))
                    <div class="mt-4 rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

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
                            <a href="{{ route('admin.pets.show', $pet->id) }}" class="block rounded-lg border p-4 transition hover:shadow-md" style="border-color: var(--brand-secondary);">
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