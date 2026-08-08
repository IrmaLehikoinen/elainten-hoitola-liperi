<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Asiakkaat
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Hoidossa olevat, huomenna saapuvat, ja kaikkien asiakkaiden haku
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Haku --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Hae asiakasta
                </h2>

                <form method="GET" action="{{ route('admin.customers.index') }}" class="mt-4 flex gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Nimi, puhelin tai sähköposti"
                        class="w-full rounded-md border-gray-300 shadow-sm"
                    >

                    <button type="submit" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-semibold">
                        Hae
                    </button>
                </form>

                @if ($search !== '')
                    <div class="mt-4 divide-y">
                        @forelse ($searchResults as $customer)
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="block py-3 hover:bg-gray-50">
                                <p class="text-sm font-medium" style="color: var(--brand-text);">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ collect([$customer->phone, $customer->email])->filter()->join(' · ') ?: 'Ei yhteystietoja' }}
                                </p>
                            </a>
                        @empty
                            <p class="py-4 text-sm text-gray-500">Ei osumia haulle "{{ $search }}".</p>
                        @endforelse
                    </div>
                @endif
            </section>

            {{-- Hoidossa nyt --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Hoidossa nyt
                </h2>

                <div class="mt-4 divide-y">
                    @forelse ($inCareCustomers as $customer)
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="block py-3 hover:bg-gray-50">
                            <p class="text-sm font-medium" style="color: var(--brand-text);">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ collect([$customer->phone, $customer->email])->filter()->join(' · ') ?: 'Ei yhteystietoja' }}
                            </p>
                        </a>
                    @empty
                        <p class="py-4 text-sm text-gray-500">Ei kenenkään lemmikkiä hoidossa juuri nyt.</p>
                    @endforelse
                </div>
            </section>

            {{-- Saapuu huomenna --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Saapuu huomenna
                </h2>

                <div class="mt-4 divide-y">
                    @forelse ($arrivingTomorrowCustomers as $customer)
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="block py-3 hover:bg-gray-50">
                            <p class="text-sm font-medium" style="color: var(--brand-text);">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ collect([$customer->phone, $customer->email])->filter()->join(' · ') ?: 'Ei yhteystietoja' }}
                            </p>
                        </a>
                    @empty
                        <p class="py-4 text-sm text-gray-500">Ei huomenna saapuvia.</p>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>