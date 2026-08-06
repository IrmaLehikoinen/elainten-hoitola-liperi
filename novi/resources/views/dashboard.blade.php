<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Hallintapaneeli</p>

            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                {{ $brand['name'] ?? 'Yrityksen hallinta' }}
            </h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

                <a
                    href="#"
                    class="block bg-white p-6 shadow-sm rounded-lg border hover:shadow-md transition"
                    style="border-color: var(--brand-secondary);"
                >
                    <p class="text-sm text-gray-500">Varaukset tänään</p>
                    <p class="mt-2 text-3xl font-semibold" style="color: var(--brand-primary);">
                        0
                    </p>
                </a>

                <a
                    href="#"
                    class="block bg-white p-6 shadow-sm rounded-lg border hover:shadow-md transition"
                    style="border-color: var(--brand-secondary);"
                >
                    <p class="text-sm text-gray-500">Tulevat varaukset</p>
                    <p class="mt-2 text-3xl font-semibold" style="color: var(--brand-primary);">
                        0
                    </p>
                </a>

                <a
                    href="#"
                    class="block bg-white p-6 shadow-sm rounded-lg border hover:shadow-md transition"
                    style="border-color: var(--brand-secondary);"
                >
                    <p class="text-sm text-gray-500">Asiakkaat</p>
                    <p class="mt-2 text-3xl font-semibold" style="color: var(--brand-primary);">
                        0
                    </p>
                </a>

                <a
                    href="#"
                    class="block bg-white p-6 shadow-sm rounded-lg border hover:shadow-md transition"
                    style="border-color: var(--brand-secondary);"
                >
                    <p class="text-sm text-gray-500">Hoidossa nyt</p>
                    <p class="mt-2 text-3xl font-semibold" style="color: var(--brand-primary);">
                        0
                    </p>
                </a>

            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">

                <section class="lg:col-span-2 bg-white p-6 shadow-sm rounded-lg">
                    <div class="flex items-center justify-between">
                        <h2
                            class="text-xl font-semibold"
                            style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                        >
                            Seuraavat varaukset
                        </h2>

                        <a href="#" class="text-sm font-medium" style="color: var(--brand-primary);">
                            Näytä kaikki
                        </a>
                    </div>

                    <div class="mt-6 rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-500">
                        Ei vielä varauksia.
                    </div>
                </section>

                <section class="bg-white p-6 shadow-sm rounded-lg">
                    <h2
                        class="text-xl font-semibold"
                        style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                    >
                        Pikatoiminnot
                    </h2>

                    <div class="mt-6 space-y-3">
                        <a href="#" class="btn-brand block rounded-md px-4 py-3 text-center font-semibold">
                            Lisää varaus
                        </a>

                        <a
                            href="#"
                            class="block rounded-md border px-4 py-3 text-center font-semibold"
                            style="border-color: var(--brand-primary); color: var(--brand-primary);"
                        >
                            Lisää asiakas
                        </a>

                        <a
                            href="#"
                            class="block rounded-md border px-4 py-3 text-center font-semibold"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                        >
                            Hallitse palveluita
                        </a>
                    </div>
                </section>

            </div>

        </div>
    </div>
</x-app-layout>