<nav x-data="{ open: false }">

    <!-- Puhelimen yläpalkki -->
    <div class="flex h-16 items-center justify-between border-b bg-white px-4 sm:hidden">
        <div onclick="window.location.href='{{ route('dashboard') }}'" class="cursor-pointer">
            <x-application-logo />
        </div>

        <button
            type="button"
            @click="open = ! open"
            class="rounded-md p-2"
            style="color: var(--brand-text);"
            aria-label="Avaa valikko"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="! open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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

    @php
        $navItems = [
            ['route' => 'dashboard', 'active' => request()->routeIs('dashboard'), 'label' => 'Etusivu', 'icon' => 'home'],
            ['route' => 'calendar.index', 'active' => request()->routeIs('calendar.*'), 'label' => 'Kalenteri', 'icon' => 'calendar'],
            ['route' => 'admin.bookings.index', 'active' => request()->routeIs('admin.bookings.index'), 'label' => 'Varaukset', 'icon' => 'check'],
            ['route' => 'admin.customers.index', 'active' => request()->routeIs('admin.customers.*'), 'label' => 'Asiakkaat', 'icon' => 'users'],
            ['route' => null, 'active' => false, 'label' => 'Palvelut', 'icon' => 'shield'],
            ['route' => null, 'active' => false, 'label' => 'Laskutus', 'icon' => 'euro'],
            ['route' => null, 'active' => false, 'label' => 'Raportit', 'icon' => 'chart'],
        ];
    @endphp

    <!-- Vasen sivuvalikko -->
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col text-white transition-transform duration-200 sm:translate-x-0"
        style="background-color: var(--brand-primary);"
    >
        <!-- Novi ja asiakkaan yrityksen nimi -->
        <div class="min-h-24 border-b border-white/15 px-6 py-5">
            <div onclick="window.location.href='{{ route('dashboard') }}'" class="inline-flex cursor-pointer items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-lg">🐾</span>
                <span class="text-lg font-semibold" style="font-family: var(--brand-heading-font);">Novi</span>
            </div>

            <p class="mt-2 text-sm leading-tight text-white/70">
                {{ $company['name'] ?? 'Yrityksen nimi' }}
            </p>
        </div>

        <!-- Päävalikko -->
        <div class="flex-1 overflow-y-auto px-3 py-6">
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-white/50">
                Hallinta
            </p>

            <div class="space-y-1">
                @foreach ($navItems as $item)
                    <div
                        @if ($item['route']) onclick="window.location.href='{{ route($item['route']) }}'" @endif
                        class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $item['active'] ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10' }}"
                    >
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center">
                            @switch($item['icon'])
                                @case('home')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5.5 10v9a1 1 0 0 0 1 1H9.5v-6h5v6H17.5a1 1 0 0 0 1-1v-9" /></svg>
                                    @break
                                @case('calendar')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><rect x="3.5" y="5" width="17" height="16" rx="2" /><path stroke-linecap="round" d="M3.5 9.5h17M8 3v4M16 3v4" /></svg>
                                    @break
                                @case('check')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><rect x="3.5" y="4" width="17" height="16" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" d="m8 12 2.5 2.5L16 9" /></svg>
                                    @break
                                @case('users')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><circle cx="9" cy="8" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5M16 8.5a2.5 2.5 0 1 0 0-5M18.5 19c0-2.5-1.7-4.4-4-4.9" /></svg>
                                    @break
                                @case('shield')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5 19 6v6c0 4.5-3 7.5-7 8.5-4-1-7-4-7-8.5V6l7-2.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" /></svg>
                                    @break
                                @case('euro')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6a6.5 6.5 0 1 0 0 12M6.5 10h7M6.5 14h6" /></svg>
                                    @break
                                @case('chart')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5h16M8 19V10M13 19V5M18 19v-7" /></svg>
                                    @break
                            @endswitch
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <p class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-white/50">
                Järjestelmä
            </p>

            <div class="space-y-1">
                <div
                    onclick="window.location.href='{{ route('admin.settings.index') }}'"
                    class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.settings.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10' }}"
                >
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><circle cx="12" cy="12" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 13.5a7.6 7.6 0 0 0 0-3l1.6-1.2-1.5-2.6-1.9.6a7.7 7.7 0 0 0-2.6-1.5L14.6 3h-3l-.4 2-.1-.1a7.6 7.6 0 0 0-2.6 1.5l-1.9-.6-1.5 2.6L6.6 10a7.6 7.6 0 0 0 0 3l-1.6 1.2 1.5 2.6 1.9-.6a7.7 7.7 0 0 0 2.6 1.5l.4 2h3l.4-2a7.7 7.7 0 0 0 2.6-1.5l1.9.6 1.5-2.6-1.6-1.2Z" /></svg>
                    </span>
                    <span>Asetukset</span>
                </div>
            </div>
        </div>

        <!-- Käyttäjä ja uloskirjautuminen -->
        <div class="border-t border-white/15 p-4">
            <div class="mb-3 flex items-center gap-2 px-2">
             <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/20">
                    <svg viewBox="0 0 24 24" fill="white" class="h-4 w-4">
                        <ellipse cx="12" cy="16" rx="5" ry="4.2" />
                        <ellipse cx="6" cy="9" rx="2.1" ry="2.6" />
                        <ellipse cx="10.5" cy="6" rx="2" ry="2.5" />
                        <ellipse cx="14.5" cy="6" rx="2" ry="2.5" />
                        <ellipse cx="18" cy="9" rx="2.1" ry="2.6" />
                    </svg>
                </span>  
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold">{{ Auth::user()->name }}</p>
                    <p class="truncate text-xs text-white/60">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full rounded-md border border-white/20 px-3 py-2 text-left text-sm font-medium text-white/90 transition hover:bg-white/10"
                >
                    Kirjaudu ulos
                </button>
            </form>
        </div>
    </aside>
</nav>