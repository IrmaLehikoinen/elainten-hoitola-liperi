<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                {{ ucfirst($day->translatedFormat('l j.n.Y')) }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kaikki tämän päivän varaukset
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <a href="{{ route('dashboard') }}" class="text-sm font-medium" style="color: var(--brand-primary);">
                ← Takaisin etusivulle
            </a>

            @forelse ($bookings as $entry)
                @php
                    $single = $entry['participants']->count() === 1;
                    $participant = $entry['participants']->first();
                    $customer = $entry['customer'];
                @endphp

                <div class="bg-white p-5 shadow-sm rounded-lg">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold" style="color: var(--brand-text);">
                                {{ $customer->name ?? 'Tuntematon asiakas' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $entry['participants']->pluck('name')->join(', ') }}
                            </p>
                        </div>

                        @if ($single && $participant->pet_id && Route::has('admin.pets.show'))
                            <a href="{{ route('admin.pets.show', $participant->pet_id) }}" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-semibold">
                                Avaa eläinkortti
                            </a>
                        @elseif (! $single && $customer && Route::has('admin.customers.show'))
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-semibold">
                                Avaa asiakaskortti
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="py-10 text-center text-gray-500">
                    Ei varauksia tälle päivälle.
                </p>
            @endforelse

        </div>
    </div>
</x-app-layout>