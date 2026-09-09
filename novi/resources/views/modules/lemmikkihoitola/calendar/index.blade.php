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
                class="btn-brand flex items-center gap-2 rounded-md px-6 py-3 text-base font-semibold shadow-md"
                @click="$dispatch('open-requirement-modal')"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Uusi varaus
            </button>   
        </div>
    </x-slot>

        <div
        id="booking-calendar-root"
        class="py-8"
        x-data="bookingCalendar"
        x-init="if (new URLSearchParams(window.location.search).get('varaa') === '1') { openRequirementModal() }"
        @open-requirement-modal.window="openRequirementModal"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-6 shadow-sm">

                               <!-- Kalenterin yläpalkki -->
                @if (session('status'))
                    <div class="mb-4 rounded-md bg-green-50 p-3 text-sm font-medium text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    @php
                        $prevAnchor = match ($calendarView) {
                            'week' => $anchorDate->copy()->subWeek(),
                            'day' => $anchorDate->copy()->subDay(),
                            default => $anchorDate->copy()->subMonth(),
                        };
                        $nextAnchor = match ($calendarView) {
                            'week' => $anchorDate->copy()->addWeek(),
                            'day' => $anchorDate->copy()->addDay(),
                            default => $anchorDate->copy()->addMonth(),
                        };
                    @endphp

                    <div class="flex items-center gap-3">
                        <div
                         onclick="window.location.href='{{ route('calendar.index', ['view' => $calendarView, 'date' => $prevAnchor->format('Y-m-d')]) }}#booking-calendar-root'"   
                            class="flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-md border"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                            </svg>
                        </div>

                        <h2
                            class="text-xl font-semibold"
                            style="
                                color: var(--brand-text);
                                font-family: var(--brand-heading-font);
                            "
                        >
                            {{ $periodLabel }}
                        </h2>

                        <div
                            onclick="window.location.href='{{ route('calendar.index', ['view' => $calendarView, 'date' => $nextAnchor->format('Y-m-d')]) }}#booking-calendar-root'"
                            class="flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-md border"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </div>

                                            <div
                            onclick="window.location.href='{{ route('calendar.index', ['view' => $calendarView, 'date' => today()->format('Y-m-d')]) }}#booking-calendar-root'"
                            class="ml-1 cursor-pointer rounded-md px-2 py-1 text-xs font-medium"
                            style="border: 1px solid var(--brand-secondary); color: var(--brand-text);"
                        >
                            Tänään
                        </div>

                        @if ($newBookingsCount > 0)
                            <span
                                @if ($firstNewBookingDate)
                                 onclick="window.location.href='{{ route('calendar.index', ['view' => 'month', 'date' => $firstNewBookingDate]) }}#booking-calendar-root'"   
                                @endif
                                class="ml-1 inline-block rounded-full px-3 py-1 text-xs font-semibold text-white"
                                style="background-color: #D98C7A; {{ $firstNewBookingDate ? 'cursor:pointer;' : '' }}"
                            >
                                {{ $newBookingsCount }} uutta varausta
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <div
                            onclick="window.location.href='{{ route('calendar.index', ['view' => 'month', 'date' => $anchorDate->format('Y-m-d')]) }}#booking-calendar-root'"
                            class="cursor-pointer rounded-md px-3 py-2 text-sm font-medium"
                            style="
                                {{ $calendarView === 'month' ? 'background-color: var(--brand-secondary);' : 'border: 1px solid var(--brand-secondary);' }}
                                color: var(--brand-text);
                            "
                        >
                            Kuukausi
                        </div>

                        <div
                            onclick="window.location.href='{{ route('calendar.index', ['view' => 'week', 'date' => $anchorDate->format('Y-m-d')]) }}#booking-calendar-root'"
                            class="cursor-pointer rounded-md px-3 py-2 text-sm font-medium"
                            style="
                                {{ $calendarView === 'week' ? 'background-color: var(--brand-secondary);' : 'border: 1px solid var(--brand-secondary);' }}
                                color: var(--brand-text);
                            "
                        >
                            Viikko
                        </div>

                        <div
                            onclick="window.location.href='{{ route('calendar.index', ['view' => 'day', 'date' => $anchorDate->format('Y-m-d')]) }}#booking-calendar-root'"
                            class="cursor-pointer rounded-md px-3 py-2 text-sm font-medium"
                            style="
                                {{ $calendarView === 'day' ? 'background-color: var(--brand-secondary);' : 'border: 1px solid var(--brand-secondary);' }}
                                color: var(--brand-text);
                            "
                        >
                            Päivä
                        </div>
                                        </div>
                </div>

            <!-- Kalenteriruudukko -->
                <div class="mt-6">
                    @include('partials.calendar-grid', ['days' => $calendarDays, 'periodStart' => $periodStart, 'view' => $calendarView])
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
                            Määritä lemmikit ja hoidon pituus.
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
                            Lemmikkien määrä
                        </label>

                        <select
                            x-model.number="animalCount"
                            @change="updateAnimalCount"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                                                      <option :value="1">1 lemmikki</option>
                            <option :value="2">2 lemmikkiä</option>
                            <option :value="3">3 lemmikkiä</option>
                            <option :value="4">4 lemmikkiä</option>
                            <option :value="5">5 lemmikkiä</option>  
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
                                    x-text="'Lemmikki ' + (index + 1)"
                                ></label>

                                <select
                                    x-model="animal.species"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="koira">Koira</option>
                                    <option value="kissa">Kissa</option>
                                    <option value="kani">Kani</option>
                                    <option value="muu">Muu lemmikki</option>
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
                            Puhelinnumero
                        </label>

                                               <input
                            type="text"
                            :value="customerPhone"
                            @input="customerPhone = formatPhoneNumber($event.target.value)"
                            @keydown.enter.prevent="searchCustomer"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="040 123 4567"
                        > 

                        <label class="mt-3 block text-sm font-medium">
                            Sähköposti
                        </label>

                        <div class="mt-1 flex gap-2">
                            <input
                                type="text"
                                x-model="customerEmail"
                                @keydown.enter.prevent="searchCustomer"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="asiakas@email.fi"
                            >

                            <button
                                type="button"
                                class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-semibold disabled:opacity-50"
                                @click="searchCustomer"
                                :disabled="searching"
                            >
                                <span x-show="!searching">Hae</span>
                                <span x-show="searching">Haetaan…</span>
                            </button>
                        </div>

                        <p class="mt-1 text-xs text-gray-400">
                            Täytä ainakin toinen. Uudelle asiakkaalle voit täyttää molemmat.
                        </p>

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
                    <p
                            class="font-semibold"
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
                          Ei lemmikkejä valittuna.  
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

                    <form x-show="conflictSwitchUrl" x-cloak method="POST" :action="conflictSwitchUrl" class="mt-1">
                        <input type="hidden" name="_token" :value="csrfToken">
                        <input type="hidden" name="redirect" :value="conflictRedirect">
                        <button type="submit" class="btn-brand rounded-md px-3 py-1.5 text-sm font-semibold">
                            Avaa Sydänpolun kalenteri tältä päivältä →
                        </button>
                    </form>

                    <div x-show="paymentUrl" x-cloak class="rounded-md border p-3" style="border-color: var(--brand-secondary);">
                        <p class="break-all text-sm" style="color: var(--brand-text);" x-text="paymentUrl"></p>

                        <button
                            type="button"
                            class="btn-brand mt-2 rounded-md px-3 py-1.5 text-sm font-semibold"
                            @click="navigator.clipboard.writeText(paymentUrl); saveMessage = 'Linkki kopioitu leikepöydälle.'"
                        >
                            Kopioi linkki
                        </button>
                    </div>   
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
                paymentUrl: '',

                customerPhone: '',
                customerEmail: '',
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
                conflictSwitchUrl: '',
                conflictRedirect: '',

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

                customersStoreUrl:
                    @json(route('admin.customers.store')),

                customersUpdateUrlBase:
                    @json(url('/admin/customers')),

                holdStoreUrl:
                    @json(route('admin.bookings.hold.store')),

                                holdReleaseUrl:
                    @json(route('admin.bookings.hold.destroy')),

                holdBeaconUrl:
                    @json(route('admin.bookings.hold.beacon')),

                                csrfToken:
                    @json(csrf_token()),

                formatPhoneNumber(value) {
                    const digits = value.replace(/\D/g, '').slice(0, 10);

                    if (digits.length > 6) {
                        return digits.slice(0, 3) + ' ' + digits.slice(3, 6) + ' ' + digits.slice(6);
                    }

                    if (digits.length > 3) {
                        return digits.slice(0, 3) + ' ' + digits.slice(3);
                    }

                    return digits;
                },

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

                    this.resultsMonth = this.availableStartDates.length > 0
                        ? new Date(this.availableStartDates[0].iso + 'T00:00:00')
                        : new Date();
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
                    this.releaseHold();
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
                        } else {
                            this.activeHoldIds = [];
                            this.saveSucceeded = false;
                            this.saveMessage = data.message || 'Tämä päivä ei olekaan enää vapaa.';
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
                    this.customerPhone = '';
                    this.customerEmail = '';
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
                    this.paymentUrl = '';

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
                                name: '',
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

                    async createAndOpenNewCustomer() {
                    const phone = this.customerPhone.trim();
                    const email = this.customerEmail.trim();

                    try {
                        const response = await fetch(this.customersStoreUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                phone: phone || null,
                                email: email || null,
                            }),
                        });

                        const createdCustomer = await response.json();

                        if (!response.ok) {
                            this.searchMessage = 'Asiakkaan luonti epäonnistui.';
                            return;
                        }

                    this.customer = createdCustomer;
                        this.customerNotFound = false;
                        this.pets = [];
                        this.buildBookingAnimals();

                        const speciesParam = this.animals.map((a) => a.species).join(',');

                        window.open(
                            this.customersUpdateUrlBase + '/' + createdCustomer.id
                                + '?fromBooking=1&species=' + encodeURIComponent(speciesParam),
                            '_blank'
                        );
                    } catch (error) {
                        this.searchMessage = 'Asiakkaan luonti epäonnistui.';
                    }
                },

                                async searchCustomer() {   
                    this.customer = null;
                    this.customerNotFound = false;
                    this.pets = [];
                    this.bookingAnimals = [];
                    this.searchMessage = '';
                    this.saveMessage = '';
                    this.saveSucceeded = false;

                    const phone = this.customerPhone.trim();
                    const email = this.customerEmail.trim();
                    const query = phone || email;

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

                    if (response.status === 404) {
                            await this.createAndOpenNewCustomer();
                            return;
                        }

                        if (!response.ok) {
                            throw new Error('Asiakashaku epäonnistui.');
                        }

                                                const data = await response.json();
                        this.customer = data;
                        this.customerEmail = data.email || this.customerEmail;
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
                     this.saveMessage = 'Ei lemmikkejä varaukselle.';   
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
                                hold_ids: this.activeHoldIds,
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

                        if (data.payment_url) {
                            this.paymentUrl = data.payment_url;
                            this.saveMessage = data.email_sent
                                ? 'Varaus tallennettu. Maksulinkki lähetettiin asiakkaalle sähköpostitse. Voit myös kopioida sen tästä varmuuden vuoksi:'
                                : 'Varaus tallennettu. Asiakkaalla ei ole sähköpostia tallennettuna — kopioi maksulinkki ja lähetä se asiakkaalle itse:';
                        } else if (data.conflict_warning) {
                            this.saveMessage = 'Varaus tallennettu. ' + data.conflict_warning;
                            this.conflictSwitchUrl = data.conflict_switch_url || '';
                            this.conflictRedirect = data.conflict_redirect || '';
                        } else {
                            this.saveMessage = 'Varaus tallennettiin onnistuneesti.';

                            window.setTimeout(() => {
                                window.location.href = this.dashboardUrl;
                            }, 900);
                        }
                    } catch (error) {
                        this.saveMessage = error.message || 'Tallennus epäonnistui.';
                    } finally {
                        this.saving = false;
                    }
                }
           }));
        });

        window.refreshBookingCustomer = function () {
            var root = document.getElementById('booking-calendar-root');

            if (root && window.Alpine) {
                var data = window.Alpine.$data(root);

                if (data && typeof data.searchCustomer === 'function') {
                    data.searchCustomer();
                }
            }
        };

        // Vapauttaa aktiiviset hold-varaukset luotettavasti myös silloin kun
        // selainikkuna suljetaan tai sivulta poistutaan kesken lomakkeen
        // täytön (ei vain kun klikataan "Peruuta"). navigator.sendBeacon on
        // selaimen oma työkalu juuri tähän tilanteeseen - se ehtii lähettää
        // pyynnön vaikka sivu on jo sulkeutumassa, toisin kuin tavallinen fetch.
        window.addEventListener('pagehide', function () {
            var root = document.getElementById('booking-calendar-root');

            if (!root || !window.Alpine) {
                return;
            }

            var data = window.Alpine.$data(root);

            if (!data || !data.activeHoldIds || !data.activeHoldIds.length) {
                return;
            }

            var formData = new FormData();
            formData.append('_token', data.csrfToken);

            data.activeHoldIds.forEach(function (id) {
                formData.append('hold_ids[]', id);
            });

            navigator.sendBeacon(data.holdBeaconUrl, formData);
        });
    </script>
</x-app-layout>