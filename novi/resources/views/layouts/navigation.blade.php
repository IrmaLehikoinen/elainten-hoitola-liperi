<nav x-data="{ open: false }">

    <!-- Puhelimen yläpalkki -->
    <div class="flex h-16 items-center justify-between border-b bg-white px-4 sm:hidden">
        <a href="{{ route('dashboard') }}">
            <x-application-logo />
        </a>

        <button
            type="button"
            @click="open = ! open"
            class="rounded-md p-2"
            style="color: var(--brand-text);"
            aria-label="Avaa valikko"
        >
            <svg
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    x-show="! open"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />

                <path
                    x-show="open"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </button>
    </div>

    <!-- Tumma tausta puhelimella -->
    <div
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="fixed inset-0 z-40 bg-black/40 sm:hidden"
    ></div>

    <!-- Vasen sivuvalikko -->
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r bg-white transition-transform duration-200 sm:translate-x-0"
        style="border-color: var(--brand-secondary);"
    >
       <!-- Novi ja asiakkaan yrityksen nimi -->
<div
    class="min-h-24 border-b px-6 py-5"
    style="border-color: var(--brand-secondary);"
>
    <a href="{{ route('dashboard') }}" class="inline-block">
    <x-application-logo />
</a>

    <p
        class="mt-2 text-xl font-semibold leading-tight"
        style="
            color: var(--brand-text);
            font-family: var(--brand-heading-font);
        "
    >
        {{ $company['name'] ?? 'Yrityksen nimi' }}
    </p>
</div>

        <!-- Päävalikko -->
        <div class="flex-1 overflow-y-auto px-4 py-6">
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Hallinta
            </p>

            <div class="space-y-1">

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                    @if(request()->routeIs('dashboard'))
                        style="background-color: var(--brand-secondary); color: var(--brand-text);"
                    @else
                        style="color: var(--brand-text);"
                    @endif
                >
                    <span class="text-lg">⌂</span>
                    <span>Etusivu</span>
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-gray-100"
                    style="color: var(--brand-text);"
                >
                    <span class="text-lg">□</span>
                    <span>Kalenteri</span>
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-gray-100"
                    style="color: var(--brand-text);"
                >
                    <span class="text-lg">✓</span>
                    <span>Varaukset</span>
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-gray-100"
                    style="color: var(--brand-text);"
                >
                    <span class="text-lg">♙</span>
                    <span>Asiakkaat</span>
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-gray-100"
                    style="color: var(--brand-text);"
                >
                    <span class="text-lg">◇</span>
                    <span>Palvelut</span>
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-gray-100"
                    style="color: var(--brand-text);"
                >
                    <span class="text-lg">€</span>
                    <span>Laskutus</span>
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-gray-100"
                    style="color: var(--brand-text);"
                >
                    <span class="text-lg">▥</span>
                    <span>Raportit</span>
                </a>
            </div>

            <p class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Järjestelmä
            </p>

            <div class="space-y-1">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-gray-100"
                    style="color: var(--brand-text);"
                >
                    <span class="text-lg">⚙</span>
                    <span>Asetukset</span>
                </a>
            </div>
        </div>

        <!-- Käyttäjä ja uloskirjautuminen -->
        <div
            class="border-t p-4"
            style="border-color: var(--brand-secondary);"
        >
            <div class="mb-3 px-2">
                <p
                    class="truncate text-sm font-semibold"
                    style="color: var(--brand-text);"
                >
                    {{ Auth::user()->name }}
                </p>

                <p class="truncate text-xs text-gray-500">
                    {{ Auth::user()->email }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full rounded-md border px-3 py-2 text-left text-sm font-medium transition hover:bg-gray-50"
                    style="
                        border-color: var(--brand-secondary);
                        color: var(--brand-text);
                    "
                >
                    Kirjaudu ulos
                </button>
            </form>
        </div>
    </aside>
</nav>