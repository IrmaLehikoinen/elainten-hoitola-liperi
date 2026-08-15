<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Etusivu
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ $today->translatedFormat('l j.n.Y') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8" x-data="dashboardPage">
      <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">  

            {{-- Rivi 1: Hoidossa tänään | Muistutukset --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <button
                    type="button"
                    @click="showTodayModal = true"
                    class="rounded-xl bg-white p-6 text-left shadow-sm transition hover:shadow-md"
                >
                    <div class="flex items-start justify-between">
                        <div>
                         <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Hoidossa tänään</p>
                            <p class="mt-2 text-lg font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                                @if ($inCareToday->count() > 0)
                                    {{ $inCareToday->pluck('name')->implode(', ') }}
                                @else
                                    Ei lemmikkejä hoidossa
                                @endif
                            </p>
                            <p class="mt-1 text-sm text-gray-500">{{ $inCareToday->count() }} lemmikkiä juuri nyt</p>   
                        </div>

                        <span
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full"
                            style="background-color: var(--brand-secondary);"
                        >
                         <svg viewBox="0 0 24 24" fill="var(--brand-primary)" class="h-5 w-5">
                                <ellipse cx="12" cy="16" rx="5" ry="4.2" />
                                <ellipse cx="6" cy="9" rx="2.1" ry="2.6" />
                                <ellipse cx="10.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="14.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="18" cy="9" rx="2.1" ry="2.6" />
                            </svg>   
                        </span>
                    </div>

                    <p class="mt-4 text-sm font-semibold" style="color: var(--brand-primary);">
                        Katso kaikki tämän päivän lemmikit →
                    </p>
                </button>

                <section class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                            Saapuvat ja lähtevät tänään
                        </h2>

                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                            style="background-color: var(--brand-secondary);"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="1.8" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h11M11 7l4 5-4 5M20 7v10" />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-3 divide-y max-h-72 overflow-y-auto">
                        <template x-for="(item, index) in (showAllEvents ? todayEvents : todayEvents.slice(0, 5))" :key="index">
                            <div class="flex items-center gap-4 py-3">
                                <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-primary);" x-text="item.time"></span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium" style="color: var(--brand-text);" x-text="item.label + (item.name ? ' – ' + item.name : '')"></p>
                                    <p class="truncate text-xs text-gray-500" x-show="item.customer" x-text="item.customer"></p>
                                </div>

                                <span
                                    x-show="item.pet_id"
                                    @click="window.location.href = '/admin/pets/' + item.pet_id"
                                    class="shrink-0 cursor-pointer text-sm font-medium"
                                    style="color: var(--brand-primary);"
                                >
                                    Avaa →
                                </span>
                            </div>
                        </template>

                        <p x-show="todayEvents.length === 0" class="py-6 text-center text-sm text-gray-500">
                            Ei tämän päivän saapumisia tai lähtöjä.
                        </p>
                    </div>

                    <button
                        type="button"
                        x-show="todayEvents.length > 5"
                        @click="showAllEvents = !showAllEvents"
                        class="mt-3 flex w-full items-center justify-center gap-1 text-sm font-medium"
                        style="color: var(--brand-primary);"
                    >
                        <span x-text="showAllEvents ? 'Näytä vähemmän' : 'Näytä kaikki'"></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 transition-transform" :class="showAllEvents ? 'rotate-180' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                </section>
            </div>

                {{-- Rivi 2: Kalenteri | Saapuvat ja lähtevät tänään --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">

                <section class="rounded-xl bg-white p-6 shadow-sm md:col-span-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                            {{ ucfirst($calendarMonth->translatedFormat('F Y')) }}
                        </h2>

                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                            style="background-color: var(--brand-secondary);"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="1.8" class="h-4 w-4">
                                <rect x="3.5" y="5" width="17" height="16" rx="2" />
                                <path stroke-linecap="round" d="M3.5 9.5h17M8 3v4M16 3v4" />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="w-full">
                            <div class="grid grid-cols-7 border-l border-t border-gray-200">

                                @foreach (['Ma', 'Ti', 'Ke', 'To', 'Pe', 'La', 'Su'] as $weekday)
                                    <div class="border-b border-r border-gray-200 px-2 py-2 text-center text-xs font-semibold text-gray-500">
                                        {{ $weekday }}
                                    </div>
                                @endforeach

                            @php $leadingBlanks = $calendarMonth->copy()->startOfMonth()->dayOfWeekIso - 1; @endphp
                                @for ($i = 0; $i < $leadingBlanks; $i++)
                                    <div class="aspect-square border-b border-r border-gray-200 bg-gray-50"></div>
                                @endfor

                                @foreach ($calendarDays as $day)
                                    @php
                                        $dayHref = $day['count'] > 0
                                            ? route('admin.calendar.day', $day['date']->format('Y-m-d'))
                                            : null;
                                
                                    @endphp

                                    <div
                                        @if ($dayHref) onclick="window.location.href='{{ $dayHref }}'" @endif
                                        class="aspect-square overflow-hidden border-b border-r border-gray-200 p-1.5 {{ $dayHref ? 'hover:bg-gray-50 cursor-pointer' : 'cursor-default' }}"   
                                    >
                                        <div class="text-xs font-medium" style="color: var(--brand-text);">
                                            {{ $day['date']->day }}
                                        </div>
                                 @foreach ($day['participants']->groupBy(fn ($p) => mb_strtolower(trim($p->species))) as $species => $group)
                                            <div
                                                class="mt-1 truncate rounded px-1.5 py-0.5 text-[11px] font-medium text-white"
                                                style="background-color: var(--brand-primary);"
                                            >
                                                {{ ucfirst($species) }} {{ $group->count() }}
                                            </div>
                         @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

               </section>

               <section class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                            Muistutukset
                        </h2>

                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                            style="background-color: var(--brand-secondary);"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="1.8" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.2M18.5 9.5c0-3.6-2.9-5.5-6.5-5.5s-6.5 1.9-6.5 5.5c0 5-2 6.5-2 6.5h17s-2-1.5-2-6.5ZM9.5 19a2.5 2.5 0 0 0 5 0" />
                            </svg>
                        </span>
                    </div>

                   <div class="mt-3 divide-y max-h-72 overflow-y-auto">
                        <template x-for="item in (showAllReminders ? reminders : reminders.slice(0, 5))" :key="item.id">
                            <div class="flex items-center gap-4 py-3">
                                <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-primary);" x-text="item.time"></span>

                                <button
                                    type="button"
                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded border border-gray-300"
                                    @click="toggle(item.id)"
                                    aria-label="Merkitse tehdyksi"
                                ></button>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium" style="color: var(--brand-text);" x-text="item.label + (item.name ? ' – ' + item.name : '')"></p>
                                    <p class="truncate text-xs text-gray-500" x-show="item.customer" x-text="item.customer"></p>
                                </div>

                                <span
                                    x-show="item.pet_id"
                                    @click="window.location.href = '/admin/pets/' + item.pet_id"
                                    class="shrink-0 cursor-pointer text-sm font-medium"
                                    style="color: var(--brand-primary);"
                                >
                                    Avaa →
                                </span>
                            </div>
                        </template>

                        <p x-show="reminders.length === 0" class="py-6 text-center text-sm text-gray-500">
                            Ei avoimia muistutuksia tänään.
                        </p>
                    </div>

                    <button
                        type="button"
                        x-show="reminders.length > 5"
                        @click="showAllReminders = !showAllReminders"
                        class="mt-3 flex w-full items-center justify-center gap-1 text-sm font-medium"
                        style="color: var(--brand-primary);"
                    >
                        <span x-text="showAllReminders ? 'Näytä vähemmän' : 'Näytä kaikki'"></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 transition-transform" :class="showAllReminders ? 'rotate-180' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                </section> 
            </div>

            {{-- Rivi 3: Nopeat toiminnot | Huomenna hoitoon tulevat --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <section class="rounded-xl bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                        Nopeat toiminnot
                    </h2>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div
                            onclick="window.location.href='{{ route('calendar.index') }}?varaa=1'"
                            class="btn-brand flex cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-semibold"
                        >
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <ellipse cx="12" cy="16" rx="5" ry="4.2" />
                                <ellipse cx="6" cy="9" rx="2.1" ry="2.6" />
                                <ellipse cx="10.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="14.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="18" cy="9" rx="2.1" ry="2.6" />
                            </svg>
                            Uusi varaus
                        </div>

                        <div
                            class="flex cursor-not-allowed items-center justify-center gap-2 rounded-lg border px-4 py-3 text-sm font-semibold opacity-60"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                        >
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <ellipse cx="12" cy="16" rx="5" ry="4.2" />
                                <ellipse cx="6" cy="9" rx="2.1" ry="2.6" />
                                <ellipse cx="10.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="14.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="18" cy="9" rx="2.1" ry="2.6" />
                            </svg>
                            Uusi lasku
                        </div>

                        <div
                            class="flex cursor-not-allowed items-center justify-center gap-2 rounded-lg border px-4 py-3 text-sm font-semibold opacity-60"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                        >
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <ellipse cx="12" cy="16" rx="5" ry="4.2" />
                                <ellipse cx="6" cy="9" rx="2.1" ry="2.6" />
                                <ellipse cx="10.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="14.5" cy="6" rx="2" ry="2.5" />
                                <ellipse cx="18" cy="9" rx="2.1" ry="2.6" />
                            </svg>
                            Uusi raportti
                        </div>
                    </div>
                </section>

                <section class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                            Huomenna hoitoon tulevat
                        </h2>

                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                            style="background-color: var(--brand-secondary);"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="1.8" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4-2.5-7-5.3-7-9a5 5 0 0 1 9-3 5 5 0 0 1 9 3c0 3.7-3 6.5-7 9-1.3.8-2.7.8-4 0Z" />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-3 divide-y max-h-[300px] overflow-y-auto">
                        @forelse ($arrivingTomorrow as $participant)
                            @php $customer = optional($participant->booking)->customer; @endphp
                            <div class="flex items-center gap-4 py-3">
                                <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-primary);">
                                    {{ optional($participant->booking)->arrival_at?->format('H:i') ?? '—' }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium" style="color: var(--brand-text);">
                                        @if ($participant->pet_id && Route::has('admin.pets.show'))
                                            <span onclick="window.location.href='{{ route('admin.pets.show', $participant->pet_id) }}'" class="cursor-pointer" style="color: var(--brand-text);">{{ $participant->name }}</span>
                                        @else
                                            {{ $participant->name }}
                                        @endif
                                        <span class="text-gray-400">· {{ $participant->species }}</span>
                                    </p>

                                    <p class="truncate text-xs text-gray-500">
                                        @if ($customer && Route::has('admin.customers.show'))
                                            <span onclick="window.location.href='{{ route('admin.customers.show', $customer->id) }}'" class="cursor-pointer" style="color: var(--brand-primary);">{{ $customer->name }}</span>
                                        @else
                                            {{ $customer->name ?? '—' }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-sm text-gray-500">
                                Ei huomenna saapuvia eläimiä.
                            </p>
                        @endforelse
                    </div>
                </section>
           </div>

    {{-- Modaali: Hoidossa tänään --}}
    <div
        x-show="showTodayModal"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 p-4"
        @keydown.escape.window="showTodayModal = false"
    >
        <div class="max-h-[85vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white p-6 shadow-xl" @click.outside="showTodayModal = false">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Hoidossa tänään
                </h2>

                <button type="button" @click="showTodayModal = false" class="rounded-md p-1 text-gray-400 hover:bg-gray-100" aria-label="Sulje">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" /></svg>
                </button>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                            <th class="pb-2 pr-4">Eläin</th>
                            <th class="pb-2 pr-4">Laji</th>
                            <th class="pb-2 pr-4">Asiakas</th>
                            <th class="pb-2 pr-4">Lähtöpäivä</th>
                            <th class="pb-2">Noutoaika</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($inCareToday as $participant)
                            @php $customer = optional($participant->booking)->customer; @endphp
                            <tr @if ($participant->end_date?->isToday()) style="background-color: var(--brand-secondary);" @endif>
                                <td class="py-2 pr-4 font-medium">
                                    @if ($participant->pet_id && Route::has('admin.pets.show'))
                                        <span onclick="window.location.href='{{ route('admin.pets.show', $participant->pet_id) }}'" class="cursor-pointer" style="color: var(--brand-primary);">
                                            {{ $participant->name }}
                                        </span>
                                    @else
                                        {{ $participant->name }}
                                    @endif
                                </td>
                                <td class="py-2 pr-4">{{ $participant->species }}</td>
                                <td class="py-2 pr-4">
                                    @if ($customer && Route::has('admin.customers.show'))
                                        <span onclick="window.location.href='{{ route('admin.customers.show', $customer->id) }}'" class="cursor-pointer" style="color: var(--brand-primary);">
                                            {{ $customer->name }}
                                        </span>
                                    @else
                                        {{ $customer->name ?? '—' }}
                                    @endif
                                </td>
                                <td class="py-2 pr-4">{{ $participant->end_date?->format('d.m.Y') }}</td>
                                <td class="py-2">{{ optional($participant->booking)->pickup_at?->format('H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">
                                    Ei hoidossa olevia eläimiä tänään.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
             </table>
            </div>
        </div>
    </div>

        </div>
    </div>

    <script>  
        document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardPage', () => ({
              reminders: @json($todayReminders),
                todayEvents: @json($todayEvents),
                showTodayModal: false,
                showAllReminders: false,
                showAllEvents: false,

                async toggle(id) {
                    try {
                        const response = await fetch(`/admin/reminders/${id}/toggle`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                        });

                        const data = await response.json();

                        if (data.done) {
                            this.reminders = this.reminders.filter(r => r.id !== id);
                        }
                    } catch (error) {
                        // Ei tehty mitään, rivi jää näkyviin jos pyyntö epäonnistui.
                    }
                },
            }));
        });
    </script>
</x-app-layout>