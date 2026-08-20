<x-layouts.public :step="4" :total-steps="5">
    <h2 class="text-2xl font-bold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
        Omat ja lemmikin tiedot
    </h2>
    <p class="text-sm" style="color: var(--brand-text); opacity: 0.6;">
        @if ($customer)
            Tarkista että tiedot ovat ajan tasalla.
        @else
            Täytä tietosi ja lemmikkiesi tiedot.
        @endif
    </p>

    <form method="POST" action="{{ route('public.booking.store') }}">
        @csrf

        <div class="public-field" style="margin-top: 28px;">
            <div class="public-pet-heading"><span>🐾</span><span>Omat tiedot</span></div>

            <div style="margin-top:16px;">
                <label class="public-field-label">Nimi</label>
                <input type="text" name="customer_name" value="{{ $customer->name ?? '' }}" class="public-input" required>
            </div>
            <div style="margin-top:16px;">
                <label class="public-field-label">Puhelin</label>
                <input type="tel" name="customer_phone" value="{{ $customer->phone ?? '' }}" class="public-input" required>
            </div>
            <div style="margin-top:16px;">
                <label class="public-field-label">Sähköposti</label>
                <input type="email" value="{{ $email }}" class="public-input" disabled style="opacity:0.6;">
            </div>
        </div>

        @php $usedPetIds = []; @endphp
        @foreach ($animals as $index => $animal)
            @php
                $existingPet = null;
                if ($customer) {
                    $existingPet = $customer->pets->first(fn ($p) => mb_strtolower($p->species) === mb_strtolower($animal['species']) && !in_array($p->id, $usedPetIds));
                    if ($existingPet) { $usedPetIds[] = $existingPet->id; }
                }
            @endphp
            <div class="public-field" style="margin-top: 32px;">
                <div class="public-pet-heading"><span>🐾</span><span>Lemmikki {{ $index + 1 }} ({{ ucfirst($animal['species']) }})</span></div>

                <input type="hidden" name="pets[{{ $index }}][species]" value="{{ $animal['species'] }}">
                @if ($existingPet)
                    <input type="hidden" name="pets[{{ $index }}][pet_id]" value="{{ $existingPet->id }}">
                @endif

                <p class="public-field-label" style="margin-top:20px; opacity:0.6; font-weight:600; text-transform:uppercase; font-size:11px;">Perustiedot</p>

                <div style="margin-top:12px;">
                    <label class="public-field-label">Nimi</label>
                    <input type="text" name="pets[{{ $index }}][name]" value="{{ $existingPet->name ?? '' }}" class="public-input" required>
                </div>
                <div style="margin-top:16px; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label class="public-field-label">Rotu</label>
                        <input type="text" name="pets[{{ $index }}][breed]" value="{{ $existingPet->breed ?? '' }}" class="public-input">
                    </div>
                    <div>
                        <label class="public-field-label">Syntymäaika</label>
                        <input type="date" name="pets[{{ $index }}][birth_date]" value="{{ $existingPet?->birth_date?->format('Y-m-d') }}" class="public-input">
                    </div>
                </div>
                <div style="margin-top:16px; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label class="public-field-label">Sukupuoli</label>
                        <select name="pets[{{ $index }}][sex]" class="public-input">
                            <option value="" @selected(empty($existingPet?->sex))>—</option>
                            <option value="uros" @selected($existingPet?->sex === 'uros')>Uros</option>
                            <option value="naaras" @selected($existingPet?->sex === 'naaras')>Naaras</option>
                        </select>
                    </div>
                    <div>
                        <label class="public-field-label">Paino (kg)</label>
                        <input type="number" step="0.1" min="0" name="pets[{{ $index }}][weight]" value="{{ $existingPet->weight ?? '' }}" class="public-input">
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <label class="public-field-label">Mikrosiru</label>
                    <input type="text" name="pets[{{ $index }}][microchip_number]" value="{{ $existingPet->microchip_number ?? '' }}" class="public-input">
                </div>

                <p class="public-field-label" style="margin-top:24px; opacity:0.6; font-weight:600; text-transform:uppercase; font-size:11px;">Terveys- ja hoitotiedot</p>

                <div style="margin-top:12px; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label class="public-field-label">Allergiat</label>
                        <textarea name="pets[{{ $index }}][allergies]" rows="2" class="public-input" style="height:auto; padding:12px 16px;">{{ $existingPet->allergies ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="public-field-label">Lääkitys</label>
                        <textarea name="pets[{{ $index }}][medications]" rows="2" class="public-input" style="height:auto; padding:12px 16px;">{{ $existingPet->medications ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="public-field-label">Ruokintaohjeet</label>
                        <textarea name="pets[{{ $index }}][feeding_instructions]" rows="2" class="public-input" style="height:auto; padding:12px 16px;">{{ $existingPet->feeding_instructions ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="public-field-label">Käytöstiedot</label>
                        <textarea name="pets[{{ $index }}][behaviour_notes]" rows="2" class="public-input" style="height:auto; padding:12px 16px;">{{ $existingPet->behaviour_notes ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="public-field-label">Eläinlääkäri (nimi)</label>
                        <input type="text" name="pets[{{ $index }}][veterinarian_name]" value="{{ $existingPet->veterinarian_name ?? '' }}" class="public-input">
                    </div>
                    <div>
                        <label class="public-field-label">Eläinlääkäri (puhelin)</label>
                        <input type="text" name="pets[{{ $index }}][veterinarian_phone]" value="{{ $existingPet->veterinarian_phone ?? '' }}" class="public-input">
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <label class="public-field-label">Hätätilanneohjeet</label>
                    <textarea name="pets[{{ $index }}][emergency_notes]" rows="2" class="public-input" style="height:auto; padding:12px 16px;">{{ $existingPet->emergency_notes ?? '' }}</textarea>
                </div>
                <div style="margin-top:16px;">
                    <label class="public-field-label">Muuta huomioitavaa</label>
                    <textarea name="pets[{{ $index }}][general_notes]" rows="3" class="public-input" style="height:auto; padding:12px 16px;">{{ $existingPet->general_notes ?? '' }}</textarea>
                </div>
                <div style="margin-top:16px;">
                    <label class="public-field-label">Lisätietoa tälle hoitojaksolle</label>
                    <textarea name="pets[{{ $index }}][booking_notes]" rows="2" class="public-input" style="height:auto; padding:12px 16px;" placeholder="Esim. erityistoiveita juuri tälle hoitojaksolle"></textarea>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn-brand w-full px-4 py-3 text-sm font-semibold" style="margin-top:32px;">
            Vahvista varaus →
        </button>
    </form>
</x-layouts.public>