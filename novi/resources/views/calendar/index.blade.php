<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" x-data>
            <div>
                <h1
                    class="text-2xl font-semibold"
                    style="
                        color: var(--brand-text);
                        font-family: var(--brand-heading-font);
                    "
                >
                    Kalenteri
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Varaukset ja vapaat ajat
                </p>
            </div>

            <button
                type="button"
                class="btn-brand rounded-md px-4 py-2 text-sm font-semibold"
                @click="$dispatch('open-requirement-modal')"
            >
                Lisää varaus
            </button>
        </div>
    </x-slot>

    <div
        class="py-8"
        x-data="bookingCalendar"
        x-init="if (new URLSearchParams(window.location.search).get('varaa') === '1') { openRequirementModal() }"
        @open-requirement-modal.window="openRequirementModal"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-6 shadow-sm">

                <!-- Kalenterin yläpalkki -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="rounded-md border px-3 py-2 text-sm"
                            style="
                                border-color: var(--brand-secondary);
                                color: var(--brand-text);
                            "
                        >
                            Edellinen
                        </button>

                        <button
                            type="button"
                            class="rounded-md border px-3 py-2 text-sm"
                            style="
                                border-color: var(--brand-secondary);
                                color: var(--brand-text);
                            "
                        >
                            Tänään
                        </button>

                        <button
                            type="button"
                            class="rounded-md border px-3 py-2 text-sm"
                            style="
                                border-color: var(--brand-secondary);
                                color: var(--brand-text);
                            "
                        >
                            Seuraava
                        </button>
                    </div>

                    <h2
                        class="text-xl font-semibold"
                        style="
                            color: var(--brand-text);
                            font-family: var(--brand-heading-font);
                        "
                    >
                        {{ ucfirst($calendarMonth->translatedFormat('F Y')) }}
                    </h2>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="rounded-md px-3 py-2 text-sm font-medium"
                            style="
                                background-color: var(--brand-secondary);
                                color: var(--brand-text);
                            "
                        >
                            Kuukausi
                        </button>

                        <button
                            type="button"
                            class="rounded-md border px-3 py-2 text-sm"
                            style="
                                border-color: var(--brand-secondary);
                                color: var(--brand-text);
                            "
                        >
                            Viikko
                        </button>

                        <button
                            type="button"
                            class="rounded-md border px-3 py-2 text-sm"
                            style="
                                border-color: var(--brand-secondary);
                                color: var(--brand-text);
                            "
                        >
                            Päivä
                        </button>
                    </div>
                </div>

                <!-- Kuukausikalenteri -->
                <div class="mt-6 overflow-x-auto">
                    <div class="min-w-[760px]">
                        <div class="grid grid-cols-7 border-l border-t border-gray-200">

          @foreach (['Ma', 'Ti', 'Ke', 'To', 'Pe', 'La', 'Su'] as $weekday)
                                <div class="border-b border-r border-gray-200 px-3 py-3 text-center text-sm font-semibold text-gray-500">
                                    {{ $weekday }}
                                </div>
                            @endforeach

                            @php $leadingBlanks = $calendarMonth->copy()->startOfMonth()->dayOfWeekIso - 1; @endphp
                            @for ($i = 0; $i < $leadingBlanks; $i++)
                                <div class="min-h-28 border-b border-r border-gray-200 bg-gray-50"></div>
                            @endfor

                            @foreach ($calendarDays as $day)
                                @php
                                    $dayHref = $day['count'] > 0
                                        ? route('admin.calendar.day', $day['date']->format('Y-m-d'))
                                        : null;
                                @endphp

                                <a href="{{ $dayHref ?? '#' }}" @unless ($dayHref) onclick="return false;" @endunless class="block min-h-28 border-b border-r border-gray-200 p-2 {{ $dayHref ? 'hover:bg-gray-50 cursor-pointer' : 'cursor-default' }}">
                                    <div class="text-sm font-medium" style="color: var(--brand-text);">
                                        {{ $day['date']->day }}
                                    </div>

                                    @if ($day['count'] === 1)
                                        @php $p = $day['participants']->first(); @endphp
                                        <div class="mt-2 truncate rounded-md px-2 py-1 text-xs font-medium" style="background-color: var(--brand-secondary); color: var(--brand-text);">
                                            {{ optional($p->booking)->arrival_at?->format('H:i') }} {{ $p->name }}
                                        </div>
                                    @elseif ($day['count'] > 1)
                                        @foreach ($day['participants']->groupBy('species') as $species => $group)
                                            <div class="mt-2 truncate rounded-md px-2 py-1 text-xs font-medium text-white" style="background-color: var(--brand-primary);">
                                                {{ $group->count() }} {{ $species }}
                                            </div>
                                        @endforeach
                                    @endif
                                </a>
                            @endforeach

                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- 1. Varaustarpeen määritys -->
        <div
            x-show="showRequirementModal"
            x-cloak
            class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="closeRequirementModal"
        >
            <div
                class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-xl"
                @click.outside="closeRequirementModal"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <h2
                            class="text-2xl font-semibold"
                            style="
                                color: var(--brand-text);
                                font-family: var(--brand-heading-font);
                            "
                        >
                            Etsi vapaa hoitoaika
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Määritä eläimet ja hoidon pituus.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-md p-2 text-gray-500 hover:bg-gray-100"
                        @click="closeRequirementModal"
                        aria-label="Sulje"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium">
                            Eläinten määrä
                        </label>

                        <select
                            x-model.number="animalCount"
                            @change="updateAnimalCount"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option :value="1">1 eläin</option>
                            <option :value="2">2 eläintä</option>
                            <option :value="3">3 eläintä</option>
                            <option :value="4">4 eläintä</option>
                            <option :value="5">5 eläintä</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <template
                            x-for="(animal, index) in animals"
                            :key="index"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium"
                                    x-text="'Eläin ' + (index + 1)"
                                ></label>

                                <select
                                    x-model="animal.species"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="koira">Koira</option>
                                    <option value="kissa">Kissa</option>
                                    <option value="kani">Kani</option>
                                    <option value="muu">Muu eläin</option>
                                </select>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">
                            Hoitomuoto
                        </label>

                        <select
                            x-model="careType"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                            @foreach ($careTypes as $careType)
                                <option value="{{ $careType->slug }}">{{ $careType->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium">
                                Hoidon pituus
                            </label>

                            <input
                                type="number"
                                min="1"
                                x-model.number="durationAmount"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Yksikkö
                            </label>

                            <select
                                x-model="durationUnit"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            >
                                <option value="days">Päivää</option>
                                <option value="weeks">Viikkoa</option>
                            </select>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn-brand w-full rounded-md px-4 py-3 text-sm font-semibold disabled:opacity-50"
                        @click="checkAvailability"
                        :disabled="checkingAvailability"
                    >
                        <span x-show="!checkingAvailability">
                            Näytä vapaat ajat
                        </span>

                        <span x-show="checkingAvailability">
                            Tarkistetaan…
                        </span>
                    </button>

                    <div
                        x-show="availabilityChecked"
                        x-cloak
                        class="space-y-3"
                    >
                        <h3
                            class="font-semibold"
                            style="color: var(--brand-text);"
                        >
                            Vapaat aloituspäivät
                        </h3>

                        <p
                            x-show="availableStartDates.length === 0"
                            class="rounded-md bg-gray-50 p-4 text-sm text-gray-500"
                        >
                            Tälle kokoonpanolle ei löytynyt vapaita aikoja.
                        </p>

                        <div x-show="availableStartDates.length > 0" x-cloak>
                            <div class="flex items-center justify-between">
                                <button
                                    type="button"
                                    class="rounded-md border px-3 py-1 text-sm"
                                    style="border-color: var(--brand-secondary); color: var(--brand-text);"
                                    @click="prevResultsMonth"
                                >
                                    ‹ Edellinen
                                </button>

                                <span
                                    class="text-sm font-semibold"
                                    style="color: var(--brand-text);"
                                    x-text="resultsMonthLabel()"
                                ></span>

                                <button
                                    type="button"
                                    class="rounded-md border px-3 py-1 text-sm"
                                    style="border-color: var(--brand-secondary); color: var(--brand-text);"
                                    @click="nextResultsMonth"
                                >
                                    Seuraava ›
                                </button>
                            </div>

                            <div class="mt-3 grid grid-cols-7 gap-1 text-center">
                                <template x-for="weekday in ['Ma','Ti','Ke','To','Pe','La','Su']" :key="weekday">
                                    <div class="text-xs font-semibold text-gray-400" x-text="weekday"></div>
                                </template>

                                <template x-for="(day, index) in resultsCalendarDays()" :key="index">
                                    <div>
                                        <button
                                            type="button"
                                            x-show="day"
                                            :disabled="!(day && day.available)"
                                            @click="day && day.available && selectAvailableDate(day.data)"
                                            class="flex h-10 w-full items-center justify-center rounded-md text-sm"
                                            :class="day && day.available
                                                ? 'font-semibold cursor-pointer'
                                                : 'text-gray-300 cursor-default'"
                                            :style="day && day.available
                                                ? 'border: 1px solid var(--brand-primary); color: var(--brand-primary);'
                                                : ''"
                                            x-text="day ? day.day : ''"
                                        ></button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Asiakas ja varauksen vahvistaminen -->
        <div
            x-show="showBookingModal"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="closeBookingModal"
        >
            <div
                class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-xl"
                @click.outside="closeBookingModal"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <h2
                            class="text-2xl font-semibold"
                            style="
                                color: var(--brand-text);
                                font-family: var(--brand-heading-font);
                            "
                        >
                            Uusi varaus
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Valittu hoitojakso:
                            <span x-text="selectedDate"></span>
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-md p-2 text-gray-500 hover:bg-gray-100"
                        @click="closeBookingModal"
                        aria-label="Sulje"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium">
                            Puhelinnumero tai sähköposti
                        </label>

                        <div class="mt-1 flex gap-2">
                            <input
                                type="text"
                                x-model="customerSearch"
                                @keydown.enter.prevent="searchCustomer"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="040 123 4567 tai asiakas@email.fi"
                            >

                            <button
                                type="button"
                                class="btn-brand rounded-md px-4 py-2 text-sm font-semibold disabled:opacity-50"
                                @click="searchCustomer"
                                :disabled="searching"
                            >
                                <span x-show="!searching">Hae</span>
                                <span x-show="searching">Haetaan…</span>
                            </button>
                        </div>

                        <p
                            x-show="searchMessage"
                            x-text="searchMessage"
                            class="mt-2 text-sm text-red-600"
                        ></p>
                    </div>

                    <div
                        x-show="customer"
                        x-cloak
                        class="rounded-lg border p-4"
                        style="border-color: var(--brand-secondary);"
                    >
                        <p class="text-xs text-gray-500">
                            Asiakas löytyi
                        </p>

                        <p
                            class="mt-1 font-semibold"
                            style="color: var(--brand-text);"
                            x-text="customer ? customer.name : ''"
                        ></p>

                        <p
                            class="mt-1 text-sm text-gray-500"
                            x-text="customer
                                ? [customer.phone, customer.email].filter(Boolean).join(' · ')
                                : ''"
                        ></p>
                    </div>

                    <div
                        x-show="customerNotFound"
                        x-cloak
                    >
                        <button
                            type="button"
                            class="rounded-md border px-4 py-2 text-sm font-semibold"
                            style="
                                border-color: var(--brand-primary);
                                color: var(--brand-primary);
                            "
                        >
                            + Luo uusi asiakaskortti
                        </button>
                    </div>

                    <div
                        x-show="customer"
                        x-cloak
                    >
                        <label class="block text-sm font-medium">
                            Lemmikit tälle varaukselle
                        </label>

                        <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <template
                                x-for="(animal, index) in bookingAnimals"
                                :key="index"
                            >
                                <div>
                                    <template x-if="!animal.isNew">
                                        <div
                                            @click="window.open(petsUpdateUrlBase + '/' + animal.petId + '?fromBooking=1', '_blank')"
                                            class="cursor-pointer rounded-lg border p-4 transition hover:shadow-md"
                                            style="border-color: var(--brand-secondary);"
                                        >
                                            <p class="font-semibold" style="color: var(--brand-primary);" x-text="animal.name"></p>
                                            <p class="text-sm text-gray-500" x-text="animal.species + (animal.breed ? ' · ' + animal.breed : '')"></p>
                                        </div>
                                    </template>

                                    <template x-if="animal.isNew">
                                        <div
                                            @click="createAndOpenNewPet(index)"
                                            class="cursor-pointer rounded-lg border border-dashed p-4 transition hover:shadow-md"
                                            style="border-color: var(--brand-primary);"
                                        >
                                            <p class="font-semibold" style="color: var(--brand-primary);">
                                                + Uusi lemmikki
                                            </p>
                                            <p class="text-sm text-gray-500" x-text="animal.species"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <p
                            x-show="bookingAnimals.length === 0"
                            class="mt-2 text-sm text-gray-500"
                        >
                            Ei eläimiä valittuna.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium">
                                Saapumispäivä
                            </label>

                            <input
                                type="date"
                                x-model="arrivalDate"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Saapumisaika
                            </label>

                            <input
                                type="time"
                                x-model="arrivalTime"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Noutopäivä
                            </label>

                            <input
                                type="date"
                                x-model="pickupDate"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Noutoaika
                            </label>

                            <input
                                type="time"
                                x-model="pickupTime"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">
                            Lisätiedot ja huomioitavat asiat
                        </label>

                        <textarea
                            x-model="notes"
                            rows="4"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="Kirjoita tähän hoitojaksoon liittyvät huomiot"
                        ></textarea>
                    </div>

                    <p
                        x-show="saveMessage"
                        x-text="saveMessage"
                        class="rounded-md px-3 py-2 text-sm font-medium"
                        :class="saveSucceeded
                            ? 'bg-green-50 text-green-700'
                            : 'bg-red-50 text-red-700'"
                    ></p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-md border px-4 py-2 text-sm font-semibold"
                        style="
                            border-color: var(--brand-secondary);
                            color: var(--brand-text);
                        "
                        @click="closeBookingModal"
                        :disabled="saving"
                    >
                        Peruuta
                    </button>

                    <button
                        type="button"
                        class="btn-brand rounded-md px-4 py-2 text-sm font-semibold disabled:opacity-50"
                        @click="saveBooking"
                        :disabled="saving"
                    >
                        <span x-show="!saving">
                            Tallenna varaus
                        </span>

                        <span x-show="saving">
                            Tallennetaan…
                        </span>
                    </button>
                </div>
            </div>
        </div>

     </div>   

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingCalendar', () => ({
                showRequirementModal: false,
                showBookingModal: false,

                animalCount: 1,
                animals: [
                    { species: 'koira' }
                ],

                durationAmount: 1,
                durationUnit: 'days',
                careType: 'full_day',

                availableStartDates: [],
                availabilityChecked: false,
                checkingAvailability: false,
                resultsMonth: null,

                selectedDate: '',
                arrivalDate: '',
                arrivalTime: '09:00',
                pickupDate: '',
                pickupTime: '17:00',
                notes: '',

                customerSearch: '',
                customer: null,
                customerNotFound: false,
                pets: [],
                bookingAnimals: [],
                activeHoldIds: [],

                searching: false,
                saving: false,
                searchMessage: '',
                saveMessage: '',
                saveSucceeded: false,

                customerSearchUrl:
                    @json(route('admin.bookings.customer-search')),

                bookingStoreUrl:
                    @json(route('admin.bookings.store')),

                availabilityUrl:
                    @json(route('admin.bookings.availability')),

                dashboardUrl:
                    @json(route('dashboard')),

                petsStoreUrl:
                    @json(route('admin.pets.store')),

             petsUpdateUrlBase:
                    @json(url('/admin/pets')),

                holdStoreUrl:
                    @json(route('admin.bookings.hold.store')),

                holdReleaseUrl:
                    @json(route('admin.bookings.hold.destroy')),

                csrfToken:
                    @json(csrf_token()),

                openRequirementModal() {
                    this.resetRequirementForm();
                    this.showRequirementModal = true;
                },

                closeRequirementModal() {
                    if (this.checkingAvailability) {
                        return;
                    }

                    this.showRequirementModal = false;
                },

                resetRequirementForm() {
                    this.animalCount = 1;
                    this.animals = [
                        { species: 'koira' }
                    ];

                    this.durationAmount = 1;
                    this.durationUnit = 'days';
                    this.careType = 'full_day';

                    this.availableStartDates = [];
                    this.availabilityChecked = false;
                    this.checkingAvailability = false;
                },

                updateAnimalCount() {
                    const count = Number(this.animalCount);

                    while (this.animals.length < count) {
                        this.animals.push({
                            species: 'dog'
                        });
                    }

                    while (this.animals.length > count) {
                        this.animals.pop();
                    }
                },

                async checkAvailability() {
                    this.checkingAvailability = true;
                    this.availabilityChecked = false;
                    this.availableStartDates = [];

                    const durationDays =
                        this.durationUnit === 'weeks'
                            ? Number(this.durationAmount) * 7
                            : Number(this.durationAmount);

                    try {
                        const response = await fetch(this.availabilityUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                animals: this.animals,
                                duration_days: durationDays,
                            }),
                        });

                        const data = await response.json();

                        this.availableStartDates = data.dates || [];
                    } catch (error) {
                        this.availableStartDates = [];
                    }

                    this.resultsMonth = new Date();
                    this.availabilityChecked = true;
                    this.checkingAvailability = false;
                },

                resultsCalendarDays() {
                    if (!this.resultsMonth) {
                        return [];
                    }

                    const year = this.resultsMonth.getFullYear();
                    const month = this.resultsMonth.getMonth();
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    const leadingBlanks = (firstDay.getDay() + 6) % 7;

                    const days = [];

                    for (let i = 0; i < leadingBlanks; i++) {
                        days.push(null);
                    }

                    for (let d = 1; d <= lastDay.getDate(); d++) {
                        const iso =
                            year + '-' +
                            String(month + 1).padStart(2, '0') + '-' +
                            String(d).padStart(2, '0');

                        const match = this.availableStartDates.find(
                            date => date.iso === iso
                        );

                        days.push({
                            day: d,
                            iso: iso,
                            available: !!match,
                            data: match || null,
                        });
                    }

                    return days;
                },

                resultsMonthLabel() {
                    if (!this.resultsMonth) {
                        return '';
                    }

                    return this.resultsMonth.toLocaleDateString('fi-FI', {
                        month: 'long',
                        year: 'numeric',
                    });
                },

                prevResultsMonth() {
                    const d = new Date(this.resultsMonth);
                    d.setMonth(d.getMonth() - 1);
                    this.resultsMonth = d;
                },

                nextResultsMonth() {
                    const d = new Date(this.resultsMonth);
                    d.setMonth(d.getMonth() + 1);
                    this.resultsMonth = d;
                },

                selectAvailableDate(date) {
                    this.showRequirementModal = false;

                    this.openBookingModal(
                        date.display,
                        date.iso
                    );
                },

             openBookingModal(displayDate, isoDate) {
                    this.resetBookingForm();

                    this.selectedDate = displayDate;
                    this.arrivalDate = isoDate;

                    const durationDays =
                        this.durationUnit === 'weeks'
                            ? Number(this.durationAmount) * 7
                            : Number(this.durationAmount);

                    const pickup = new Date(
                        isoDate + 'T12:00:00'
                    );

                    pickup.setDate(
                        pickup.getDate() + durationDays
                    );

                    this.pickupDate =
                        pickup.getFullYear() +
                        '-' +
                        String(
                            pickup.getMonth() + 1
                        ).padStart(2, '0') +
                        '-' +
                        String(
                            pickup.getDate()
                        ).padStart(2, '0');

                    this.showBookingModal = true;

                    this.createHold(isoDate, durationDays);
                },

                async createHold(isoDate, durationDays) {
                    try {
                        const response = await fetch(this.holdStoreUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                animals: this.animals,
                                start_date: isoDate,
                                duration_days: durationDays,
                            }),
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.activeHoldIds = data.hold_ids || [];
                        }
                    } catch (error) {
                        this.activeHoldIds = [];
                    }
                },

                releaseHold() {
                    if (!this.activeHoldIds.length) {
                        return;
                    }

                    const ids = this.activeHoldIds;
                    this.activeHoldIds = [];

                    fetch(this.holdReleaseUrl, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        body: JSON.stringify({ hold_ids: ids }),
                    }).catch(() => {});
                },

                closeBookingModal() {
                    if (this.saving) {
                        return;
                    }

                    this.releaseHold();
                    this.showBookingModal = false;
                },   

                resetBookingForm() {
                    this.customerSearch = '';
                    this.customer = null;
                    this.customerNotFound = false;
           this.pets = [];
                    this.bookingAnimals = [];
                    this.activeHoldIds = [];

                    this.selectedDate = '';
                    this.arrivalDate = '';
                    this.arrivalTime = '09:00';
                    this.pickupDate = '';
                    this.pickupTime = '17:00';
                    this.notes = '';

                    this.searching = false;
                    this.saving = false;
                    this.searchMessage = '';
                    this.saveMessage = '';
                    this.saveSucceeded = false;
                },

                buildBookingAnimals() {
                    const petsBySpecies = {};
                    const normalizeSpecies = (value) => (value || '').toString().trim().toLowerCase();

                    this.pets.forEach((pet) => {
                        const key = normalizeSpecies(pet.species);

                        if (!petsBySpecies[key]) {
                            petsBySpecies[key] = [];
                        }

                        petsBySpecies[key].push(pet);
                    });

                    this.bookingAnimals = this.animals.map((animal) => {
                        const key = normalizeSpecies(animal.species);
                        const candidates = petsBySpecies[key] || [];
                        const matched = candidates.shift();

                        if (matched) {
                            return {
                                petId: matched.id,
                                species: animal.species,
                                name: matched.name || '',
                                breed: matched.breed || '',
                                isNew: false,
                            };
                        }

                        return {
                            petId: null,
                            species: animal.species,
                            name: '',
                            breed: '',
                            isNew: true,
                        };
                    });
                },

                async createAndOpenNewPet(index) {
                    const animal = this.bookingAnimals[index];
                    const name = window.prompt('Lemmikin nimi?');

                    if (!name || !name.trim()) {
                        return;
                    }

                    try {
                        const response = await fetch(this.petsStoreUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                customer_id: this.customer.id,
                                name: name.trim(),
                                species: animal.species,
                            }),
                        });

                        const createdPet = await response.json();

                        if (!response.ok) {
                            this.saveMessage = 'Lemmikin luonti epäonnistui.';
                            return;
                        }

                        this.bookingAnimals[index] = {
                            petId: createdPet.id,
                            species: createdPet.species,
                            name: createdPet.name,
                            breed: createdPet.breed || '',
                            isNew: false,
                        };

                        this.pets.push(createdPet);

     window.open(this.petsUpdateUrlBase + '/' + createdPet.id + '?fromBooking=1', '_blank');
                    } catch (error) {
                        this.saveMessage = 'Lemmikin luonti epäonnistui.';
                    }
                },
                async searchCustomer() {
                    this.customer = null;
                    this.customerNotFound = false;
                    this.pets = [];
                    this.bookingAnimals = [];
                    this.searchMessage = '';

                    const query = this.customerSearch.trim();

                    if (!query) {
                        this.searchMessage = 'Kirjoita puhelinnumero tai sähköposti.';
                        return;
                    }

                    this.searching = true;

                    try {
                        const url = new URL(this.customerSearchUrl, window.location.origin);
                        url.searchParams.set('q', query);

                        const response = await fetch(url, {
                            headers: { Accept: 'application/json' },
                        });

                        if (!response.ok) {
                            throw new Error('Asiakashaku epäonnistui.');
                        }

                        const data = await response.json();

                        if (!data) {
                            this.customerNotFound = true;
                            this.searchMessage = 'Asiakasta ei löytynyt.';
                            return;
                        }

                        this.customer = data;
                        this.pets = Array.isArray(data.pets) ? data.pets : [];
                        this.buildBookingAnimals();
                    } catch (error) {
                        this.searchMessage = error.message || 'Asiakashaku epäonnistui.';
                    } finally {
                        this.searching = false;
                    }
                },

                async saveBooking() {
                    this.saveMessage = '';
                    this.saveSucceeded = false;

                    if (!this.customer) {
                        this.saveMessage = 'Hae ja valitse ensin asiakas.';
                        return;
                    }

                    if (!this.bookingAnimals.length) {
                        this.saveMessage = 'Ei eläimiä varaukselle.';
                        return;
                    }

                    if (this.bookingAnimals.some((animal) => animal.isNew)) {
                        this.saveMessage = 'Täytä ensin kaikki uudet lemmikit klikkaamalla niiden kortteja.';
                        return;
                    }

                    if (
                        !this.arrivalDate ||
                        !this.arrivalTime ||
                        !this.pickupDate ||
                        !this.pickupTime
                    ) {
                        this.saveMessage = 'Täytä saapumis- ja noutoaika.';
                        return;
                    }

                    const arrivalAt = this.arrivalDate + 'T' + this.arrivalTime;
                    const pickupAt = this.pickupDate + 'T' + this.pickupTime;

                    if (new Date(pickupAt) < new Date(arrivalAt)) {
                        this.saveMessage = 'Noutoaika ei voi olla ennen saapumisaikaa.';
                        return;
                    }

                    this.saving = true;

                    try {
                        const resolvedAnimals = this.bookingAnimals.map((animal) => ({
                            pet_id: animal.petId,
                        }));

                        const response = await fetch(this.bookingStoreUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                customer_id: this.customer.id,
                                animals: resolvedAnimals,
                                arrival_at: arrivalAt,
                                pickup_at: pickupAt,
                                care_type: this.careType,
                                notes: this.notes,
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const errors = data.errors
                                ? Object.values(data.errors).flat().join(' ')
                                : null;

                            throw new Error(errors || data.message || 'Tallennus epäonnistui.');
                        }

         this.releaseHold();

                        this.saveSucceeded = true;
                        this.saveMessage = 'Varaus tallennettiin onnistuneesti.';

                        window.setTimeout(() => {
                            window.location.href = this.dashboardUrl;
                        }, 900);
                    } catch (error) {
                        this.saveMessage = error.message || 'Tallennus epäonnistui.';
                    } finally {
                        this.saving = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>