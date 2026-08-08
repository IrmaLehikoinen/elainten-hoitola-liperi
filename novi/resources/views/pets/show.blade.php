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

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Perustiedot --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Perustiedot
                </h2>

                <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Laji</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $pet->species ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Rotu</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $pet->breed ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Syntymäaika</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $pet->birth_date?->format('d.m.Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Sukupuoli</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $pet->sex ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Paino</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $pet->weight ? $pet->weight.' kg' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Mikrosiru</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">{{ $pet->microchip_number ?: '—' }}</dd>
                    </div>
                </dl>
            </section>

            {{-- Terveys- ja hoitotiedot --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Terveys- ja hoitotiedot
                </h2>

                <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Allergiat</dt>
                        <dd class="mt-1 text-sm whitespace-pre-line" style="color: var(--brand-text);">{{ $pet->allergies ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Lääkitys</dt>
                        <dd class="mt-1 text-sm whitespace-pre-line" style="color: var(--brand-text);">{{ $pet->medications ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Ruokintaohjeet</dt>
                        <dd class="mt-1 text-sm whitespace-pre-line" style="color: var(--brand-text);">{{ $pet->feeding_instructions ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Käytöstiedot</dt>
                        <dd class="mt-1 text-sm whitespace-pre-line" style="color: var(--brand-text);">{{ $pet->behaviour_notes ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Eläinlääkäri</dt>
                        <dd class="mt-1 text-sm" style="color: var(--brand-text);">
                            {{ collect([$pet->veterinarian_name, $pet->veterinarian_phone])->filter()->join(' · ') ?: '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Hätätilanneohjeet</dt>
                        <dd class="mt-1 text-sm whitespace-pre-line" style="color: var(--brand-text);">{{ $pet->emergency_notes ?: '—' }}</dd>
                    </div>
                </dl>
            </section>

            {{-- Kaksi erillistä muistiinpanokenttää --}}
            <section class="bg-white p-6 shadow-sm rounded-lg">
                <h2
                    class="text-xl font-semibold"
                    style="font-family: var(--brand-heading-font); color: var(--brand-text);"
                >
                    Muistiinpanot
                </h2>

                <form method="POST" action="{{ route('admin.pets.update', $pet) }}" class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    @csrf
                    @method('PATCH')

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

                    <div class="lg:col-span-2">
                        <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                            Tallenna muistiinpanot
                        </button>
                    </div>
                </form>
            </section>

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
                                    {{ $reminder->title ?: \App\Http\Controllers\DashboardController::typeLabel($reminder->type) }}
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
                                <option value="medication">Lääke</option>
                                <option value="feeding">Ruokinta</option>
                                <option value="wash">Pesu</option>
                                <option value="nails">Kynsien leikkaus</option>
                                <option value="vet">Eläinlääkäri</option>
                                <option value="walk">Ulkoilutus</option>
                                <option value="other">Muu tehtävä</option>
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
</x-app-layout>