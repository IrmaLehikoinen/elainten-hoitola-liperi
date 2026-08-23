<x-layouts.public :step="1" :total-steps="6">
    <div x-data="{
        animalCount: 1,
        animals: [{ species: 'koira' }],
        updateCount() {
            const count = Number(this.animalCount);
            while (this.animals.length < count) { this.animals.push({ species: 'koira' }); }
            while (this.animals.length > count) { this.animals.pop(); }
        }
    }">
                @if (session('booking_error'))
            <div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B; padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:20px;">
                {{ session('booking_error') }}
            </div>
        @endif

        <h2 class="text-2xl font-bold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
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

    
</x-layouts.public>