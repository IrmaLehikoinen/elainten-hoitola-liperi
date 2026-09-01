<x-app-layout>
    <div class="p-6 max-w-md">
        <h1 class="text-xl font-semibold">Asetukset</h1>
        <p class="mt-1 text-sm text-gray-500">Nämä tiedot näkyvät kuiteilla, laskuilla ja julkisella kurssisivulla.</p>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('kurssit.settings.company-info.update') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Virallinen nimi</label>
                <input type="text" name="official_name" value="{{ $company->settings['official_name'] ?? '' }}"
                    placeholder="Esim. Sydänpolku Oy" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Puhelin</label>
                <input type="text" name="phone" value="{{ $company->phone }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Y-tunnus</label>
                <input type="text" name="business_id" value="{{ $company->settings['business_id'] ?? '' }}"
                    placeholder="1234567-8" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Osoite</label>
                <input type="text" name="address" value="{{ $company->settings['address'] ?? '' }}"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Pankkitili (IBAN)</label>
                <input type="text" name="iban" value="{{ $company->settings['iban'] ?? '' }}"
                    placeholder="FI12 3456 7890 1234 56" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Maksuehto (pv netto)</label>
                <input type="number" min="1" max="90" name="payment_term_days"
                    value="{{ $company->settings['payment_term_days'] ?? 14 }}" class="mt-1 w-32 rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">ALV-prosentti</label>
                <input type="number" step="0.1" min="0" max="100" name="vat_percentage"
                    value="{{ $company->settings['vat_percentage'] ?? 25.5 }}" class="mt-1 w-32 rounded-md border-gray-300 shadow-sm">
            </div>

            <div class="flex gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Pääväri</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input type="color" id="primary_color_picker" value="{{ $company->primary_color ?? '#3F4F3A' }}"
                            class="h-10 w-16 rounded-md border-gray-300 shadow-sm"
                            oninput="document.getElementById('primary_color_text').value = this.value">
                        <input type="text" name="primary_color" id="primary_color_text" value="{{ $company->primary_color ?? '#3F4F3A' }}"
                            placeholder="#3F4F3A" class="w-28 rounded-md border-gray-300 shadow-sm text-sm"
                            oninput="if (/^#([0-9A-Fa-f]{6})$/.test(this.value)) { document.getElementById('primary_color_picker').value = this.value; }">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Toissijainen väri</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input type="color" id="secondary_color_picker" value="{{ $company->secondary_color ?? '#D8C6BD' }}"
                            class="h-10 w-16 rounded-md border-gray-300 shadow-sm"
                            oninput="document.getElementById('secondary_color_text').value = this.value">
                        <input type="text" name="secondary_color" id="secondary_color_text" value="{{ $company->secondary_color ?? '#D8C6BD' }}"
                            placeholder="#D8C6BD" class="w-28 rounded-md border-gray-300 shadow-sm text-sm"
                            oninput="if (/^#([0-9A-Fa-f]{6})$/.test(this.value)) { document.getElementById('secondary_color_picker').value = this.value; }">
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-400">Näitä värejä käytetään kuiteissa, laskuissa ja julkisella kurssisivulla.</p>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Fontti</label>
                <select name="font_pair" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    @foreach ($fontOptions as $key => $option)
                        <option value="{{ $key }}" @selected($selectedFontPair === $key)>{{ $option['label'] }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">Näkyy julkisen kurssisivun otsikoissa ja leipätekstissä.</p>
            </div>

            <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                Tallenna
            </button>
        </form>
    </div>
</x-app-layout>