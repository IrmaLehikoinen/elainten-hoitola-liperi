<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                {{ $pet->name }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}
                @if ($pet->customer && Route::has('admin.customers.show'))
                    · <a href="{{ route('admin.customers.show', $pet->customer->id) }}" style="color: var(--brand-primary);">{{ $pet->customer->name }}</a>
                @elseif ($pet->customer)
                    · {{ $pet->customer->name }}
                @endif
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                   @if (request('fromBooking'))
                <div class="rounded-md p-4" style="background-color: var(--brand-secondary);">
                 <button
                        type="button"
                        onclick="handleBackToBooking()"
                        class="btn-brand rounded-md px-4 py-2 text-sm font-semibold"
                    >
                        ← Takaisin asiakaskorttiin (sulje tämä välilehti)
                    </button>  
                </div>
            @elseif (request('from') === 'day' && request('date'))
                <div
                    onclick="window.location.href='{{ route('admin.calendar.day', request('date')) }}'"
                    class="cursor-pointer text-sm font-medium"
                    style="color: var(--brand-primary);"
                >
                    ← Takaisin päivänäkymään
                </div>
            @elseif (request('from') === 'dashboard')
                <div
                    onclick="window.location.href='{{ route('dashboard') }}'"
                    class="cursor-pointer text-sm font-medium"
                    style="color: var(--brand-primary);"
                >
                    ← Takaisin etusivulle
                </div>
            @elseif (request('from') === 'customer' && request('customer'))
                <div
                    onclick="window.location.href='{{ route('admin.customers.show', array_filter(['customer' => request('customer'), 'from' => request('origFrom'), 'date' => request('origDate'), 'booking_id' => request('origBookingId')])) }}'"
                    class="cursor-pointer text-sm font-medium"
                    style="color: var(--brand-primary);"
                >
                    ← Takaisin asiakaskorttiin
                </div>
            @endif 

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif    

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm font-medium text-red-700">
                    <p>Tarkista lomakkeen tiedot:</p>
                    <ul class="mt-1 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

           <form method="POST" action="{{ route('admin.pets.update', $pet) }}" id="pet-update-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="from_booking" value="{{ request('fromBooking') ? '1' : '' }}">

                {{-- Perustiedot --}}
                <section class="bg-white p-6 shadow-sm rounded-lg">
                    <h2
                        class="text-xl font-semibold"
                        style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                    >
                        Perustiedot
                    </h2>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                            <input type="text" name="name" value="{{ old('name', $pet->name) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Laji</label>
                            <input type="text" name="species" value="{{ old('species', $pet->species) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Rotu</label>
                            <input type="text" name="breed" value="{{ old('breed', $pet->breed) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Syntymäaika</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', optional($pet->birth_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Sukupuoli</label>
                            <select name="sex" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                                <option value="" @selected(old('sex', $pet->sex) === null)>—</option>
                                <option value="uros" @selected(old('sex', $pet->sex) === 'uros')>Uros</option>
                                <option value="naaras" @selected(old('sex', $pet->sex) === 'naaras')>Naaras</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Paino (kg)</label>
                            <input type="number" step="0.1" min="0" name="weight" value="{{ old('weight', $pet->weight) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Mikrosiru</label>
                            <input type="text" name="microchip_number" value="{{ old('microchip_number', $pet->microchip_number) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </section>

                {{-- Terveys- ja hoitotiedot --}}
                <section class="mt-6 bg-white p-6 shadow-sm rounded-lg">
                    <h2
                        class="text-xl font-semibold"
                        style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                    >
                        Terveys- ja hoitotiedot
                    </h2>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Allergiat</label>
                            <textarea name="allergies" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('allergies', $pet->allergies) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Lääkitys</label>
                            <textarea name="medications" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('medications', $pet->medications) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Ruokintaohjeet</label>
                            <textarea name="feeding_instructions" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('feeding_instructions', $pet->feeding_instructions) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Käytöstiedot</label>
                            <textarea name="behaviour_notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('behaviour_notes', $pet->behaviour_notes) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Eläinlääkäri (nimi)</label>
                            <input type="text" name="veterinarian_name" value="{{ old('veterinarian_name', $pet->veterinarian_name) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Eläinlääkäri (puhelin)</label>
                            <input type="text" name="veterinarian_phone" value="{{ old('veterinarian_phone', $pet->veterinarian_phone) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hätätilanneohjeet</label>
                            <textarea name="emergency_notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('emergency_notes', $pet->emergency_notes) }}</textarea>
                        </div>
                    </div>
                </section>

                {{-- Kaksi erillistä muistiinpanokenttää --}}
                <section class="mt-6 bg-white p-6 shadow-sm rounded-lg">
                    <h2
                        class="text-xl font-semibold"
                        style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                    >
                        Muistiinpanot
                    </h2>

                    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold" style="color: var(--brand-text);">
                                Asiakkaan tiedot
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Nämä tiedot näkyvät asiakkaalle.</p>

                            <textarea
                                name="general_notes"
                                rows="6"
                                class="mt-2 w-full rounded-md border-gray-300 shadow-sm"
                            >{{ old('general_notes', $pet->general_notes) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold" style="color: var(--brand-text);">
                                Hoitolan muistiinpanot
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Näkyvät vain yrittäjälle. Asiakas ei näe näitä koskaan.</p>

                            <textarea
                                name="internal_notes"
                                rows="6"
                                class="mt-2 w-full rounded-md border-gray-300 shadow-sm bg-amber-50"
                            >{{ old('internal_notes', $pet->internal_notes) }}</textarea>
                        </div>
                    </div>
                </section>

            <div class="mt-6">
                    <button type="submit" class="btn-brand rounded-md px-6 py-3 text-sm font-semibold">
                     Tallenna lemmikkikortti   
                    </button>
                </div>
            </form>

            <form
                method="POST"
                action="{{ route('admin.pets.destroy', $pet) }}"
                onsubmit="return confirm('Poistetaanko {{ $pet->name ?: 'tämä lemmikki' }} kokonaan? Tätä ei voi perua.');"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="rounded-md border px-4 py-2 text-sm font-semibold"
                    style="border-color: #b91c1c; color: #b91c1c;"
                >
                    Poista lemmikki
                </button>
            </form>

            {{-- Hoitojakson muistutukset --}}
            <section class="bg-white p-6 shadow-sm rounded-lg" x-data="petReminders">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Hoitojakson muistutukset
                </h2>

                <div class="mt-4 divide-y">
                    @forelse ($pet->reminders as $reminder)
                        <div class="flex items-center gap-4 py-3" x-show="!removedState[{{ $reminder->id }}]" x-cloak>
                            <button
                                type="button"
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded border"
                                :class="doneState[{{ $reminder->id }}] ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300'"
                                @click="toggle({{ $reminder->id }})"
                                aria-label="Merkitse tehdyksi"
                            >
                                <span x-show="doneState[{{ $reminder->id }}]" x-cloak class="text-xs">✓</span>
                            </button>

                            <div class="flex-1" :class="doneState[{{ $reminder->id }}] ? 'opacity-40 line-through' : ''">
                                <p class="text-sm font-medium" style="color: var(--brand-text);">
                                 {{ $reminder->title ?: \App\Modules\Lemmikkihoitola\Http\Controllers\DashboardController::typeLabel($reminder->type) }}   
                                </p>
                                <p class="text-xs text-gray-500">{{ $reminder->due_at->format('d.m.Y H:i') }}</p>
                            </div>

                            <button
                                type="button"
                                class="btn-brand rounded-md px-4 py-2 text-sm font-semibold"
                                @click="remove({{ $reminder->id }})"
                            >
                                Poista
                            </button>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-gray-500">Ei vielä muistutuksia tälle hoitojaksolle.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('admin.reminders.store') }}" class="mt-6 border-t pt-6">
                    @csrf
                    <input type="hidden" name="pet_id" value="{{ $pet->id }}">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium">Tyyppi</label>
                            <select name="type" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                                @foreach ($reminderTypes as $reminderType)
                                    <option value="{{ $reminderType->slug }}">{{ $reminderType->label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium">Kuvaus (valinnainen)</label>
                            <input
                                type="text"
                                name="title"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="Esim. Aamulääke 2 tablettia"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Ajankohta</label>
                            <input
                                type="datetime-local"
                                name="due_at"
                                required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-brand mt-4 rounded-md px-4 py-2 text-sm font-semibold">
                        Lisää muistutus
                    </button>
                </form>
            </section>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('petReminders', () => ({
                doneState: @json($pet->reminders->pluck('done_at', 'id')->map(fn ($v) => $v !== null)),
                removedState: {},

                async remove(id) {
                    if (!confirm('Poistetaanko muistutus?')) {
                        return;
                    }

                    try {
                        await fetch(`/admin/reminders/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                        });

                        this.removedState[id] = true;
                    } catch (error) {
                        // Ei tehty mitään, rivi jää näkyviin jos pyyntö epäonnistui.
                    }
                },

                async toggle(id) {
                    this.doneState[id] = !this.doneState[id];

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
                        this.doneState[id] = data.done;
                    } catch (error) {
                        this.doneState[id] = !this.doneState[id];
                    }
                },
           }));
        });
    </script>

    <script>
        (function () {
            var form = document.getElementById('pet-update-form');
            var formDirty = false;

            if (form) {
                form.addEventListener('input', function () {
                    formDirty = true;
                });

                form.addEventListener('change', function () {
                    formDirty = true;
                });

                form.addEventListener('submit', function () {
                    formDirty = false;
                });
            }

         window.handleBackToBooking = function () {
                if (formDirty) {
                    alert('Tallenna muutokset ensin ennen kuin palaat varaukseen.');
                    return;
                }

                if (window.opener && !window.opener.closed) {
                    window.opener.location.reload();
                }

                window.close();
            };   
        })();
    </script>
</x-app-layout>