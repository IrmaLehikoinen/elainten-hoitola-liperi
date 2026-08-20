<x-layouts.public :step="1" :total-steps="5">
    <div x-data="{
        animalCount: 1,
        animals: [{ species: 'koira' }],
        updateCount() {
            const count = Number(this.animalCount);
            while (this.animals.length < count) { this.animals.push({ species: 'koira' }); }
            while (this.animals.length > count) { this.animals.pop(); }
        }
    }">
        <h2 class="text-2xl font-bold" style="font-family: var(--brand-heading-font); color: var(--brand-text); white-space: nowrap;">
            Kerro meille lemmikeistäsi ja hoidon kestosta
        </h2>     
                    <p class="text-sm" style="color: var(--brand-text); opacity: 0.6;">Täytä tiedot niin näet heti vapaana olevat ajat</p>

        <form method="POST" action="{{ route('public.booking.availability') }}" class="space-y-8">
            @csrf

                        <div class="public-field">
                <label class="public-field-label">Valitse lemmikkien määrä</label>
                <select x-model.number="animalCount" @change="updateCount" class="public-input" style="border-color: var(--brand-primary); border-width: 2px;">
                    <option :value="1">1 lemmikki</option>
                    <option :value="2">2 lemmikkiä</option>
                    <option :value="3">3 lemmikkiä</option>
                    <option :value="4">4 lemmikkiä</option>
                    <option :value="5">5 lemmikkiä</option>
                </select>
            </div>

                        <template x-for="(animal, index) in animals" :key="index">
                <div class="public-field">
                    <div class="public-pet-heading">
                        <svg width="22" height="19" viewBox="-2.25 0.5 21.47 21.47" fill="currentColor">
                            <g transform="translate(-5,2) rotate(-10) scale(0.62)">
                                <circle cx="7.5" cy="9" r="2.1"/>
                                <circle cx="12" cy="6.8" r="2.1"/>
                                <circle cx="16.5" cy="9" r="2.1"/>
                                <ellipse cx="12" cy="15.5" rx="5.5" ry="4.5"/>
                            </g>
                            <g transform="translate(10,4) rotate(28) scale(0.62)">
                                <circle cx="7.5" cy="9" r="2.1"/>
                                <circle cx="12" cy="6.8" r="2.1"/>
                                <circle cx="16.5" cy="9" r="2.1"/>
                                <ellipse cx="12" cy="15.5" rx="5.5" ry="4.5"/>
                            </g>
                        </svg>
                                            <span x-text="'Lemmikki ' + (index + 1)"></span>
                    </div>

                    <label class="public-field-label">Valitse laji</label>
                    <select x-model="animal.species" :name="'animals[' + index + '][species]'" class="public-input">
                        <option value="koira">Koira</option>
                        <option value="kissa">Kissa</option>
                        <option value="kani">Kani</option>
                        <option value="muu">Muu lemmikki</option>
                    </select>
                </div>
            </template>

                        <div class="public-field">
                    <label class="public-field-label">Valitse hoitomuoto</label>
                <select name="care_type" class="public-input">
                    @foreach ($careTypes as $careType)
                        <option value="{{ $careType->slug }}">{{ $careType->label }}</option>
                    @endforeach
                </select>
            </div>

                        <div class="public-field">
                    <label class="public-field-label">Valitse hoidon pituus</label>
                <div style="display:grid; grid-template-columns: 34% 1fr; gap: 10px;">
                    <input type="number" name="duration_amount" min="1" value="1" class="public-input">
                    <select name="duration_unit" class="public-input">
                        <option value="days">Päivää</option>
                        <option value="weeks">Viikkoa</option>
                    </select>
                </div>
            </div>

                        <div class="public-brand-strip">
                <p>Kodinomaista ja turvallista hoitoa lemmikillesi.</p>
            </div>

            <button type="submit" class="btn-brand w-full px-4 py-3 text-sm font-semibold">
                <span style="display:inline-flex; align-items:center; justify-content:center; gap:10px; width:100%;">
                                    <svg width="40" height="34" viewBox="-2.25 0.5 21.47 21.47" fill="white">
                                                    <g transform="translate(-5,2) rotate(-10) scale(0.62)">
                            <circle cx="7.5" cy="9" r="2.1"/>
                            <circle cx="12" cy="6.8" r="2.1"/>
                            <circle cx="16.5" cy="9" r="2.1"/>
                            <ellipse cx="12" cy="15.5" rx="5.5" ry="4.5"/>
                        </g>
                        <g transform="translate(10,4) rotate(28) scale(0.62)">
                            <circle cx="7.5" cy="9" r="2.1"/>
                            <circle cx="12" cy="6.8" r="2.1"/>
                            <circle cx="16.5" cy="9" r="2.1"/>
                            <ellipse cx="12" cy="15.5" rx="5.5" ry="4.5"/>
                        </g>
                    </svg>
                    <span>Näytä vapaat ajat →</span>
                </span>
            </button>
        </form>
    </div>

        <x-slot:footer>
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
            @foreach ([
                ['heart', 'Rakkaudella hoidettu', 'Lemmikkisi on hyvissä käsissä'],
                ['shield', 'Turvallinen ympäristö', 'Rauhallinen hoitoympäristö'],
                ['camera', 'Päivityksiä', 'Saat kuvia ja kuulumisia hoidon aikana'],
                ['award', 'Ammattitaidolla', 'Kokemusta ja koulutusta'],
            ] as [$icon, $title, $desc])
                <div class="text-center">
                                                            <div class="mx-auto flex items-center justify-center" style="width:44px;height:44px;border-radius:999px;background:var(--brand-secondary);color:white;">
                        @if ($icon === 'heart')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9-8.5C1.2 9.5 2.5 6 6 6c2 0 3.5 1.2 4 2.5.5-1.3 2-2.5 4-2.5 3.5 0 4.8 3.5 3 6.5-2.5 4.15-9 8.5-9 8.5z"/></svg>
                        @elseif ($icon === 'shield')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z"/></svg>
                        @elseif ($icon === 'camera')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8h3l1.5-2h7L17 8h3a1 1 0 011 1v9a1 1 0 01-1 1H4a1 1 0 01-1-1V9a1 1 0 011-1z"/><circle cx="12" cy="13" r="3.5"/></svg>
                        @else
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="9" r="5"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5L7 21l5-2.5L17 21l-2-7.5"/></svg>
                        @endif
                    </div>
                    <div class="mt-2 text-xs font-semibold" style="color: var(--brand-text);">{{ $title }}</div>
                    <div class="text-xs" style="color: var(--brand-text); opacity: 0.6;">{{ $desc }}</div>
                </div>
            @endforeach
        </div>
    </x-slot:footer>
</x-layouts.public>