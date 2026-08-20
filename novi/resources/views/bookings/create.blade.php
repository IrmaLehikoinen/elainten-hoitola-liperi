<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Varaa hoitoaika
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Vaihe <span x-text="step"></span> / 6
            </p>
        </div>
    </x-slot>

    <div class="py-8" x-data="bookingWizard">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <a href="{{ route('dashboard') }}" class="text-sm font-medium" style="color: var(--brand-primary);">
                ← Peruuta ja palaa etusivulle
            </a>

            <div class="mt-6 bg-white p-6 shadow-sm rounded-lg">

                {{-- VAIHE 1: eläimet, hoitomuoto, kesto --}}
                <div x-show="step === 1" x-cloak>
                    <h2 class="text-xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                     1. Lemmikit ja hoidon kesto   
                    </h2>

                    <div class="mt-4 space-y-3">
                        <template x-for="(animal, index) in animals" :key="index">
                            <div class="flex items-center gap-2">
                                <select x-model="animal.species" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="Koira">Koira</option>
                                    <option value="Kissa">Kissa</option>
                                    <option value="Kani">Kani</option>
                                    <option value="Muu">Muu lemmikki</option>
                                </select>

                             <button
                                    type="button"
                                    class="shrink-0 rounded-md border px-3 py-2 text-sm"
                                    style="border-color: var(--brand-secondary); color: var(--brand-text);"
                                    @click="removeAnimal(index)"
                                    x-show="animals.length > 1"
                                >
                                    Poista
                                </button> 
                        
                            </div>
                        </template>

                        <button
                            type="button"
                            class="text-sm font-semibold"
                            style="color: var(--brand-primary);"
                            @click="addAnimal"
                        >
                         + Lisää lemmikki   
                        </button>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium">Hoitomuoto</label>
                        <select x-model="careType" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            <option value="full_day">Päivähoito</option>
                            <option value="overnight">Yöhoito</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium">Hoidon pituus (vuorokautta)</label>
                        <input
                            type="number"
                            min="1"
                            x-model.number="durationDays"
                            class="mt-1 w-32 rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <button
                        type="button"
                        class="btn-brand mt-6 w-full rounded-md px-4 py-3 text-sm font-semibold disabled:opacity-50"
                        @click="fetchAvailability"
                        :disabled="loadingDates"
                    >
                        <span x-show="!loadingDates">Näytä vapaat ajat</span>
                        <span x-show="loadingDates">Tarkistetaan vapaita aikoja…</span>
                    </button>
                </div>

                {{-- VAIHE 2: vapaat aloituspäivät --}}
                <div x-show="step === 2" x-cloak>
                    <h2 class="text-xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                        2. Valitse aloituspäivä
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Vain päivät joihin koko varaus mahtuu näytetään.
                    </p>

                    <p x-show="availableDates.length === 0" class="mt-6 rounded-md bg-gray-50 p-4 text-sm text-gray-500">
                        Ei vapaita aikoja lähitulevaisuudessa tällä kokoonpanolla.
                    </p>

                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <template x-for="date in availableDates" :key="date.iso">
                            <button
                                type="button"
                                class="rounded-md border px-4 py-3 text-sm font-semibold"
                                style="border-color: var(--brand-primary); color: var(--brand-primary);"
                                @click="selectStartDate(date)"
                                x-text="date.display"
                            ></button>
                        </template>
                    </div>

                    <button
                        type="button"
                        class="mt-6 text-sm font-medium"
                        style="color: var(--brand-text);"
                        @click="step = 1"
                    >
                        ← Takaisin
                    </button>
                </div>

                {{-- VAIHE 3: hoitojakson vahvistus --}}
                <div x-show="step === 3" x-cloak>
                    <h2 class="text-xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                        3. Hoitojakso
                    </h2>

                    <div class="mt-4 rounded-lg border p-4" style="border-color: var(--brand-secondary);">
                        <p class="text-sm text-gray-500">Alkaa</p>
                        <p class="font-semibold" style="color: var(--brand-text);" x-text="selectedStartDisplay"></p>

                        <p class="mt-3 text-sm text-gray-500">Päättyy</p>
                        <p class="font-semibold" style="color: var(--brand-text);" x-text="endDateDisplay"></p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button
                            type="button"
                            class="rounded-md border px-4 py-2 text-sm font-medium"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                            @click="step = 2"
                        >
                            ← Takaisin
                        </button>

                        <button
                            type="button"
                            class="btn-brand flex-1 rounded-md px-4 py-2 text-sm font-semibold"
                            @click="step = 4"
                        >
                            Varaa hoitojakso
                        </button>
                    </div>
                </div>

                {{-- VAIHE 4: asiakas --}}
                <div x-show="step === 4" x-cloak>
                    <h2 class="text-xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                        4. Syötä yhteystiedot
                    </h2>

                  <div class="mt-4 space-y-3">
                        <input
                            type="text"
                            x-model="contactName"
                            class="w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="Asiakkaan nimi"
                        >

                        <input
                            type="text"
                            x-model="customerSearch"
                            @keydown.enter.prevent="submitContact"
                            class="w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="Puhelinnumero tai sähköposti"
                        >
                    </div>

                    <p x-show="searchMessage" x-text="searchMessage" class="mt-2 text-sm text-red-600"></p>

                    <div x-show="customer" x-cloak class="mt-4 rounded-lg border p-4" style="border-color: var(--brand-secondary);">
                        <p class="text-xs text-gray-500">Asiakaskortti</p>
                        <p class="font-semibold" style="color: var(--brand-text);" x-text="customer ? customer.name : ''"></p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button
                            type="button"
                            class="rounded-md border px-4 py-2 text-sm font-medium"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                            @click="step = 3"
                        >
                            ← Takaisin
                        </button>

                        <button
                            type="button"
                            class="btn-brand flex-1 rounded-md px-4 py-2 text-sm font-semibold disabled:opacity-50"
                            @click="submitContact"
                            :disabled="searching"
                        >
                            <span x-show="!searching">Hae asiakaskortti</span>
                            <span x-show="searching">Haetaan…</span>
                        </button>
                    </div>
                </div>

                {{-- VAIHE 5: asiakaskortti ja lemmikit --}}
                <div x-show="step === 5" x-cloak>
                    <h2 class="text-xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                        5. Asiakaskortti
                    </h2>

                    <div class="mt-4 rounded-lg border p-4" style="border-color: var(--brand-secondary);">
                        <p class="font-semibold text-lg" style="color: var(--brand-text);" x-text="customer ? customer.name : 'Nimeä ei löytynyt'"></p>
                        <p class="mt-1 text-sm text-gray-500" x-text="customer ? [customer.phone, customer.email].filter(Boolean).join(' · ') : ''"></p>
                    </div>

                    {{-- Lemmikkikortit ruudukkona --}}
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2" x-show="activePetIndex === null" x-cloak>
                        <template x-for="(assignment, index) in petAssignments" :key="index">
                            <button
                                type="button"
                                class="block w-full rounded-lg border p-4 text-left transition hover:shadow-md"
                                style="border-color: var(--brand-secondary);"
                                @click="activePetIndex = index"
                            >
                                <p class="font-semibold" style="color: var(--brand-primary);" x-text="assignment.saved ? assignment.newPet.name : assignment.species"></p>
                                <p class="text-sm text-gray-500" x-text="assignment.saved ? assignment.species + ' · tiedot tallennettu' : 'Klikkaa täyttääksesi tiedot'"></p>
                            </button>
                        </template>
                    </div>

                    {{-- Yksittäisen lemmikin täysi kortti --}}
                    <template x-for="(assignment, index) in petAssignments" :key="'form-' + index">
                        <div x-show="activePetIndex === index" x-cloak class="mt-4 rounded-lg border p-4" style="border-color: var(--brand-secondary);">
                            <p class="text-sm font-medium" style="color: var(--brand-text);"x-text="'Lemmikki ' + (index + 1) + ' (' + assignment.species + ')'"></p>

                            <select x-model="assignment.pet_id" x-show="!assignment.showNewPetForm && customerPets.length > 0" class="mt-2 w-full rounded-md border-gray-300 shadow-sm">
                             <option value="">Valitse asiakkaan lemmikki</option>   
                                <template x-for="pet in customerPets" :key="pet.id">
                                    <option :value="String(pet.id)" x-text="pet.name + ' – ' + pet.species"></option>
                                </template>
                            </select>

                            <button
                                type="button"
                                class="mt-2 text-sm font-semibold"
                                style="color: var(--brand-primary);"
                                x-show="!assignment.showNewPetForm && customerPets.length > 0"
                                @click="assignment.showNewPetForm = true"
                            >
                                + Luo uusi lemmikkikortti
                            </button>

                            <div x-show="assignment.showNewPetForm || customerPets.length === 0" x-cloak class="mt-2 space-y-4">

                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <input type="text" x-model="assignment.newPet.name" placeholder="Nimi *" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <input type="text" x-model="assignment.newPet.breed" placeholder="Rotu" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <input type="date" x-model="assignment.newPet.birth_date" placeholder="Syntymäaika" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <select x-model="assignment.newPet.sex" class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">Sukupuoli</option>
                                        <option value="uros">Uros</option>
                                        <option value="naaras">Naaras</option>
                                    </select>
                                    <input type="number" step="0.1" min="0" x-model="assignment.newPet.weight" placeholder="Paino (kg)" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <input type="text" x-model="assignment.newPet.microchip_number" placeholder="Mikrosiru" class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>

                                <textarea x-model="assignment.newPet.allergies" placeholder="Allergiat" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                <textarea x-model="assignment.newPet.medications" placeholder="Lääkitys" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                <textarea x-model="assignment.newPet.feeding_instructions" placeholder="Ruokintaohjeet" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                <textarea x-model="assignment.newPet.behaviour_notes" placeholder="Käytöstiedot" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>

                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <input type="text" x-model="assignment.newPet.veterinarian_name" placeholder="Eläinlääkäri" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <input type="text" x-model="assignment.newPet.veterinarian_phone" placeholder="Eläinlääkärin puhelin" class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>

                                <textarea x-model="assignment.newPet.emergency_notes" placeholder="Hätätilanneohjeet" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>

                                <div class="flex flex-wrap items-center gap-3">
                                    <button
                                        type="button"
                                        class="btn-brand rounded-md px-4 py-2 text-sm font-semibold disabled:opacity-50"
                                        @click="createPetFor(index)"
                                        :disabled="assignment.creating || assignment.saved || !assignment.newPet.name"
                                    >
                                        <span x-show="!assignment.creating && !assignment.saved">Tallenna lemmikkikortti</span>
                                        <span x-show="assignment.creating">Tallennetaan…</span>
                                        <span x-show="assignment.saved">Tallennettu</span>
                                    </button>

                                    <span x-show="assignment.saved" x-cloak class="text-sm font-medium text-green-700">
                                        Tallennus onnistui
                                    </span>

                                    <button
                                        type="button"
                                        class="rounded-md border px-4 py-2 text-sm font-semibold"
                                        style="border-color: var(--brand-secondary); color: var(--brand-text);"
                                        @click="activePetIndex = null"
                                    >
                                        ← Palaa asiakaskorttiin
                                    </button>
                                </div>
                            </div>

                            <button
                                type="button"
                                x-show="!assignment.showNewPetForm && customerPets.length > 0"
                                class="mt-4 rounded-md border px-4 py-2 text-sm font-semibold"
                                style="border-color: var(--brand-secondary); color: var(--brand-text);"
                                @click="activePetIndex = null"
                            >
                                ← Palaa asiakaskorttiin
                            </button>
                        </div>
                    </template>

               <div class="mt-6">
                        <button
                            type="button"
                            class="btn-brand w-full rounded-md px-4 py-2 text-sm font-semibold disabled:opacity-50"
                            :disabled="!allPetsAssigned"
                            @click="step = 6"
                        >
                            Valitse lisäpalvelut
                        </button>
                    </div>
                </div>

                {{-- VAIHE 6: lisäpalvelut ja hinta --}}
                <div x-show="step === 6" x-cloak>
                    <h2 class="text-xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                        6. Lisäpalvelut ja hinta
                    </h2>

                    <div class="mt-4">
                    <label class="block text-sm font-medium">Hoitohinta / vrk / lemmikki</label>    
                        <input type="number" step="0.01" min="0" x-model.number="dailyRate" class="mt-1 w-32 rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div class="mt-4 space-y-2">
                        @foreach ($services as $service)
                            <label class="flex items-center justify-between rounded-md border p-3 text-sm" style="border-color: var(--brand-secondary);">
                                <span>
                                    <input type="checkbox" value="{{ $service->id }}" @change="toggleService({{ $service->id }})">
                                    {{ $service->name }}
                                </span>
                                <span>{{ number_format((float) $service->price, 2) }} €</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6 rounded-lg p-4" style="background-color: var(--brand-secondary);">
                        <div class="flex justify-between text-sm">
                        <span>Hoito (<span x-text="petAssignments.length"></span> lemmikkiä × <span x-text="durationDays"></span> vrk)</span>    
                            <span x-text="careSubtotal.toFixed(2) + ' €'"></span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span>Lisäpalvelut</span>
                            <span x-text="servicesSubtotal.toFixed(2) + ' €'"></span>
                        </div>
                        <div class="mt-2 flex justify-between border-t pt-2 text-base font-semibold" style="border-color: var(--brand-primary);">
                            <span>Yhteensä</span>
                            <span x-text="totalPrice.toFixed(2) + ' €'"></span>
                        </div>
                    </div>

                    <p x-show="saveMessage" x-text="saveMessage" class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700"></p>

                    <div class="mt-6 flex gap-3">
                        <button
                            type="button"
                            class="rounded-md border px-4 py-2 text-sm font-medium"
                            style="border-color: var(--brand-secondary); color: var(--brand-text);"
                            @click="step = 5"
                            :disabled="saving"
                        >
                            ← Takaisin
                        </button>

                        <button
                            type="button"
                            class="btn-brand flex-1 rounded-md px-4 py-2 text-sm font-semibold disabled:opacity-50"
                            @click="saveBooking"
                            :disabled="saving"
                        >
                            <span x-show="!saving">Tallenna varaus</span>
                            <span x-show="saving">Tallennetaan…</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

  <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingWizard', () => ({
                step: 1,

                animals: [{ species: 'Koira' }],
                careType: 'full_day',
                durationDays: 1,

                loadingDates: false,
                availableDates: [],

                selectedStartIso: '',
                selectedStartDisplay: '',
                endDateIso: '',
                endDateDisplay: '',

                contactName: '',
                customerSearch: '',
                searching: false,
                customer: null,
                searchMessage: '',

                petAssignments: [],
                customerPets: [],
                activePetIndex: null,

                services: @json($services->map(fn ($s) => ['id' => $s->id, 'price' => (float) $s->price])),
                dailyRate: @json((float) $defaultDailyRate),
                selectedServiceIds: [],

                saving: false,
                saveMessage: '',

                csrfToken: @json(csrf_token()),

                addAnimal() {
                    this.animals.push({ species: 'Koira' });
                },

                removeAnimal(index) {
                    this.animals.splice(index, 1);
                },

                async fetchAvailability() {
                    this.loadingDates = true;
                    this.availableDates = [];

                    try {
                        const response = await fetch(@json(route('admin.bookings.availability')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                animals: this.animals,
                                duration_days: this.durationDays,
                            }),
                        });

                        const data = await response.json();
                        this.availableDates = data.dates || [];
                    } finally {
                        this.loadingDates = false;
                        this.step = 2;
                    }
                },

                selectStartDate(date) {
                    this.selectedStartIso = date.iso;
                    this.selectedStartDisplay = date.display;

                    const end = new Date(date.iso + 'T00:00:00');
                    end.setDate(end.getDate() + this.durationDays - 1);

                    const pad = n => String(n).padStart(2, '0');
                    this.endDateIso = end.getFullYear() + '-' + pad(end.getMonth() + 1) + '-' + pad(end.getDate());
                    this.endDateDisplay = pad(end.getDate()) + '.' + pad(end.getMonth() + 1) + '.' + end.getFullYear();

                    this.petAssignments = this.animals.map(a => ({
                        species: a.species,
                        pet_id: '',
                        showNewPetForm: false,
                        creating: false,
                        saved: false,
                        error: '',
                        newPet: {
                            name: '',
                            species: a.species,
                            breed: '',
                            birth_date: '',
                            sex: '',
                            weight: '',
                            microchip_number: '',
                            allergies: '',
                            medications: '',
                            feeding_instructions: '',
                            behaviour_notes: '',
                            veterinarian_name: '',
                            veterinarian_phone: '',
                            emergency_notes: '',
                        },
                    }));

                    this.activePetIndex = null;
                    this.step = 3;
                },

                async submitContact() {
                    const query = this.customerSearch.trim();

                    if (!query) {
                        this.searchMessage = 'Kirjoita puhelinnumero tai sähköposti.';
                        return;
                    }

                    this.searching = true;
                    this.searchMessage = '';

                    try {
                        const searchUrl = @json(route('admin.bookings.customer-search')) + '?q=' + encodeURIComponent(query);
                        const response = await fetch(searchUrl, { headers: { Accept: 'application/json' } });
                      const data = await response.json();

                        if (data && data.id) {
                            this.customer = data;
                            this.customerPets = Array.isArray(data.pets) ? data.pets : [];
                        } else {
                            const isEmail = query.includes('@');

                            const createResponse = await fetch(@json(route('admin.customers.store')), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken,
                                },
                                body: JSON.stringify({
                                    name: this.contactName.trim() || 'Uusi asiakas',
                                    phone: isEmail ? null : query,
                                    email: isEmail ? query : null,
                                }),
                            });

                            const created = await createResponse.json();

                            if (!createResponse.ok) {
                                const errors = created.errors ? Object.values(created.errors).flat().join(' ') : null;
                                throw new Error(errors || created.message || 'Asiakkaan luonti epäonnistui.');
                            }

                            this.customer = created;
                            this.customerPets = [];
                        }

                        this.step = 5;
                    } catch (error) {
                        this.searchMessage = error.message || 'Toiminto epäonnistui, yritä uudelleen.';
                    } finally {
                        this.searching = false;
                    }
                },

                async createPetFor(index) {
                    const assignment = this.petAssignments[index];
                    assignment.creating = true;
                    assignment.error = '';

                    try {
                        const response = await fetch(@json(route('admin.pets.store')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                customer_id: this.customer.id,
                                ...assignment.newPet,
                            }),
                        });

                        const pet = await response.json();

                        if (!response.ok) {
                            const errors = pet.errors ? Object.values(pet.errors).flat().join(' ') : null;
                            throw new Error(errors || pet.message || 'Tallennus epäonnistui.');
                        }

                        this.customerPets.push(pet);
                        assignment.pet_id = String(pet.id);
                        assignment.showNewPetForm = false;
                        assignment.saved = true;
                    } catch (error) {
                        assignment.error = error.message || 'Tallennus epäonnistui.';
                    } finally {
                        assignment.creating = false;
                    }
                },

                get allPetsAssigned() {
                    return this.petAssignments.length > 0 && this.petAssignments.every(a => a.pet_id);
                },

                get careSubtotal() {
                    return this.petAssignments.length * this.durationDays * this.dailyRate;
                },

                get servicesSubtotal() {
                    return this.services
                        .filter(s => this.selectedServiceIds.includes(s.id))
                        .reduce((sum, s) => sum + s.price, 0);
                },

                get totalPrice() {
                    return this.careSubtotal + this.servicesSubtotal;
                },

                toggleService(id) {
                    const i = this.selectedServiceIds.indexOf(id);

                    if (i === -1) {
                        this.selectedServiceIds.push(id);
                    } else {
                        this.selectedServiceIds.splice(i, 1);
                    }
                },

                async saveBooking() {
                    this.saving = true;
                    this.saveMessage = '';

                    try {
                        const response = await fetch(@json(route('admin.bookings.wizard-store')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                customer_id: this.customer.id,
                                care_type: this.careType,
                                start_date: this.selectedStartIso,
                                end_date: this.endDateIso,
                                animals: this.petAssignments.map(a => ({
                                    pet_id: a.pet_id,
                                    daily_rate: this.dailyRate,
                                })),
                                service_ids: this.selectedServiceIds,
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Tallennus epäonnistui.');
                        }

                        window.location.href = '/admin/customers/' + this.customer.id;
                    } catch (error) {
                        this.saveMessage = error.message || 'Tallennus epäonnistui.';
                    } finally {
                        this.saving = false;
                    }
                },
            }));
        });
    </script>
</x-app-layout>