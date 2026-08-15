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

    <div class="py-8" x-data="todayReminders">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- 1a. Saapuvat ja lähtevät tänään --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Saapuvat ja lähtevät tänään
                </h2>

                <div class="mt-4 divide-y">
                    @forelse ($todayEvents as $item)
                        <div class="flex items-center gap-4 py-3">
                            <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-primary);">
                                {{ $item['time'] }}
                            </span>

                            <div class="flex-1">
                                <p class="text-sm font-medium" style="color: var(--brand-text);">
                                    {{ $item['label'] }}{{ $item['name'] ? ' – '.$item['name'] : '' }}
                                </p>

                                @if ($item['customer'])
                                    <p class="text-xs text-gray-500">{{ $item['customer'] }}</p>
                                @endif
                            </div>

                            @if ($item['pet_id'] && Route::has('admin.pets.show'))
                                <a href="{{ route('admin.pets.show', $item['pet_id']) }}" class="text-sm font-medium" style="color: var(--brand-primary);">
                                    Avaa →
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">
                            Ei tämän päivän saapumisia tai lähtöjä.
                        </p>
                    @endforelse
                </div>
            </section>
{{-- 1b. Hoidossa tänään --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Hoidossa tänään
                </h2>

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
                                <<tr @if ($participant->end_date?->isToday()) style="background-color: var(--brand-secondary);" @endif>
                                    <td class="py-2 pr-4 font-medium">
                                        @if ($participant->pet_id && Route::has('admin.pets.show'))
                                            <a href="{{ route('admin.pets.show', $participant->pet_id) }}" style="color: var(--brand-primary);">
                                                {{ $participant->name }}
                                            </a>
                                        @else
                                            {{ $participant->name }}
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ $participant->species }}</td>
                                    <td class="py-2 pr-4">
                                        @if ($customer && Route::has('admin.customers.show'))
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" style="color: var(--brand-primary);">
                                                {{ $customer->name }}
                                            </a>
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
            </section>

            {{-- 1c. Muistutukset --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Muistutukset
                </h2>

                <div class="mt-4 divide-y">
                    <template x-for="item in reminders" :key="item.id">
                        <div class="flex items-center gap-4 py-3">
                            <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-primary);" x-text="item.time"></span>

                            <button
                                type="button"
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded border border-gray-300"
                                @click="toggle(item.id)"
                                aria-label="Merkitse tehdyksi"
                            ></button>

                            <div class="flex-1">
                                <p class="text-sm font-medium" style="color: var(--brand-text);" x-text="item.label + (item.name ? ' – ' + item.name : '')"></p>
                                <p class="text-xs text-gray-500" x-show="item.customer" x-text="item.customer"></p>
                            </div>

                            <a :href="'/admin/pets/' + item.pet_id" x-show="item.pet_id" class="text-sm font-medium" style="color: var(--brand-primary);">
                                Avaa →
                            </a>
                        </div>
                    </template>

                    <p x-show="reminders.length === 0" class="py-6 text-center text-sm text-gray-500">
                        Ei avoimia muistutuksia tänään.
                    </p>
                </div>
            </section>

            {{-- 3. Hoitoon huomenna --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Hoitoon huomenna
                </h2>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                                <th class="pb-2 pr-4">Eläin</th>
                                <th class="pb-2 pr-4">Laji</th>
                                <th class="pb-2 pr-4">Asiakas</th>
                                <th class="pb-2 pr-4">Saapumispäivä</th>
                                <th class="pb-2">Saapumisaika</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($arrivingTomorrow as $participant)
                                @php $customer = optional($participant->booking)->customer; @endphp
                                <tr>
                                    <td class="py-2 pr-4 font-medium">
                                        @if ($participant->pet_id && Route::has('admin.pets.show'))
                                            <a href="{{ route('admin.pets.show', $participant->pet_id) }}" style="color: var(--brand-primary);">
                                                {{ $participant->name }}
                                            </a>
                                        @else
                                            {{ $participant->name }}
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ $participant->species }}</td>
                                    <td class="py-2 pr-4">
                                        @if ($customer && Route::has('admin.customers.show'))
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" style="color: var(--brand-primary);">
                                                {{ $customer->name }}
                                            </a>
                                        @else
                                            {{ $customer->name ?? '—' }}
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ $participant->start_date?->format('d.m.Y') }}</td>
                                    <td class="py-2">{{ optional($participant->booking)->arrival_at?->format('H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        Ei huomenna saapuvia eläimiä.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- 4. Iso kuukausikalenteri --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    {{ ucfirst($calendarMonth->translatedFormat('F Y')) }}
                </h2>

                <div class="mt-4 overflow-x-auto">
                    <div class="min-w-[760px]">
                        <div class="grid grid-cols-7 border-l border-t border-gray-200">

                            @foreach (['Ma', 'Ti', 'Ke', 'To', 'Pe', 'La', 'Su'] as $weekday)
                                <div class="border-b border-r border-gray-200 px-3 py-2 text-center text-xs font-semibold text-gray-500">
                                    {{ $weekday }}
                                </div>
                            @endforeach

                            @php $leadingBlanks = $calendarMonth->copy()->startOfMonth()->dayOfWeekIso - 1; @endphp
                            @for ($i = 0; $i < $leadingBlanks; $i++)
                                <div class="min-h-24 border-b border-r border-gray-200 bg-gray-50"></div>
                            @endfor

                            @foreach ($calendarDays as $day)
                                @php
                                    $dayHref = $day['count'] > 0
                                        ? route('admin.calendar.day', $day['date']->format('Y-m-d'))
                                        : null;
                                @endphp

                                <a href="{{ $dayHref ?? '#' }}" @unless ($dayHref) onclick="return false;" @endunless class="block min-h-24 border-b border-r border-gray-200 p-2 {{ $dayHref ? 'hover:bg-gray-50 cursor-pointer' : 'cursor-default' }}">
                                    <div class="text-sm font-medium" style="color: var(--brand-text);">
                                        {{ $day['date']->day }}
                                    </div>

                                    @if ($day['count'] === 1)
                                        @php $p = $day['participants']->first(); @endphp
                                        <div
                                            class="mt-1 truncate rounded px-2 py-1 text-xs font-medium"
                                            style="background-color: var(--brand-secondary); color: var(--brand-text);"
                                        >
                                            {{ optional($p->booking)->arrival_at?->format('H:i') }} {{ $p->name }}
                                        </div>
                                    @elseif ($day['count'] > 1)
                                        @foreach ($day['participants']->groupBy('species') as $species => $group)
                                            <div
                                                class="mt-1 truncate rounded px-2 py-1 text-xs font-medium text-white"
                                                style="background-color: var(--brand-primary);"
                                            >
                                                {{ $group->count() }} {{ $species }}
                                            </div>
                                        @endforeach
                                    @endif
                                </a>
                            @endforeach

                        </div>
                    </div>
                </div>
                    <div class="mt-6 text-center">
                    <a href="{{ route('calendar.index') }}?varaa=1" class="btn-brand inline-block whitespace-nowrap rounded-md px-10 py-5 text-lg font-semibold">
                        Varaa hoitoaika
                    </a>
                </div>
            </section>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('todayReminders', () => ({
                reminders: @json($todayReminders),

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