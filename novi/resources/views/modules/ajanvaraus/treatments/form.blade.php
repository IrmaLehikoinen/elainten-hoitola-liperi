        <x-app-layout>
    <div class="p-6 max-w-md">
        <a href="{{ route('ajanvaraus.treatments.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-800">← Takaisin hoitoihin</a>
        <h1 class="mt-2 text-xl font-semibold">{{ $treatment->exists ? 'Muokkaa hoitoa' : 'Uusi hoito' }}</h1>

                @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

                    <form method="POST" action="{{ $treatment->exists ? route('ajanvaraus.treatments.update', $treatment) : route('ajanvaraus.treatments.store') }}" class="mt-6 space-y-4">
            @csrf
            @if ($treatment->exists) @method('PATCH') @endif

                        <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                <input type="text" name="name" value="{{ old('name', $treatment->name) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

                                    <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Lyhyt kuvaus</label>
                <input type="text" name="short_description" value="{{ old('short_description', $treatment->short_description) }}" maxlength="1000" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                <p class="mt-1 text-xs text-gray-400">Enintään 1000 merkkiä.</p>
                @error('short_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

                        <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Muistiinpano (vain sinulle, ei näy asiakkaalle)</label>
                <textarea name="internal_note" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('internal_note', $treatment->internal_note) }}</textarea>
            </div>

                        <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Otsikko / kategoria</label>
                <select name="treatment_category_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">— ei otsikkoa —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('treatment_category_id', $treatment->treatment_category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('treatment_category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

                <div class="flex gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Kesto (min)</label>
                    <input type="number" min="5" step="5" name="duration_minutes" value="{{ old('duration_minutes', $treatment->duration_minutes) }}" required class="mt-1 w-28 rounded-md border-gray-300 shadow-sm">
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Kapasiteetti (max)</label>
                    <input type="number" min="1" name="capacity" value="{{ old('capacity', $treatment->capacity ?? 1) }}" required class="mt-1 w-28 rounded-md border-gray-300 shadow-sm">
                    @error('capacity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hinta (€)</label>
                    <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $treatment->price) }}" class="mt-1 w-28 rounded-md border-gray-300 shadow-sm">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Väri kalenterissa</label>
                <div class="mt-1 flex items-center gap-2">
                    <input type="color" id="color_picker" value="{{ old('color', $treatment->color ?? '#7CAB33') }}" class="h-10 w-16 rounded-md border-gray-300 shadow-sm" oninput="document.getElementById('color_text').value = this.value">
                    <input type="text" name="color" id="color_text" value="{{ old('color', $treatment->color ?? '#7CAB33') }}" class="w-28 rounded-md border-gray-300 shadow-sm text-sm" oninput="if (/^#([0-9A-Fa-f]{6})$/.test(this.value)) { document.getElementById('color_picker').value = this.value; }">
                </div>
            </div>

                        <div class="rounded-md border border-gray-200 p-4">
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Ryhmän minimiosallistujat (valinnainen)</label>
                <p class="mt-1 text-xs text-gray-400">Jätä tyhjäksi jos ei ryhmäminimiä (esim. yksilöhoidot).</p>
                <div class="mt-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500">Minimi osallistujat, jotta pidetään</label>
                        <input type="number" min="0" name="min_participants" value="{{ old('min_participants', $treatment->min_participants) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Varoitusviesti X päivää ennen jos minimi ei täyty</label>
                        <input type="number" min="0" name="warning_days_before" value="{{ old('warning_days_before', $treatment->warning_days_before) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $treatment->is_active ?? true)) class="rounded border-gray-300">
                                <label for="is_active" class="text-sm text-gray-700">Hoito on varattavissa verkosta</label>
            </div>

            <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">Tallenna</button>
        </form>

        @if ($treatment->exists)
            <div class="mt-10 border-t pt-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Viikoittainen aikataulu</h2>
                <p class="mt-1 text-xs text-gray-400">Näinä aikoina hoito on automaattisesti varattavissa joka viikko.</p>

                <div class="mt-3 space-y-2">
                    @forelse ($treatment->availabilityRules as $rule)
                        <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2 text-sm">
                            <span>{{ ['Sunnuntai','Maanantai','Tiistai','Keskiviikko','Torstai','Perjantai','Lauantai'][$rule->weekday] }}, klo {{ substr($rule->start_time, 0, 5) }}–{{ substr($rule->end_time, 0, 5) }}</span>
                            <form method="POST" action="{{ route('ajanvaraus.rules.destroy', $rule) }}" onsubmit="return confirm('Poistetaanko tämä viikkoaika?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">Poista</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Ei vielä viikoittaisia aikoja.</p>
                    @endforelse
                </div>

                                @error('start_time')
                <p class="mt-2 text-sm text-red-600">{!! $message !!}</p>    
                @enderror

                <form method="POST" action="{{ route('ajanvaraus.rules.store', $treatment) }}" class="mt-4 flex items-end gap-2">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Viikonpäivä</label>
                        <select name="weekday" class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                            @foreach (['Sunnuntai','Maanantai','Tiistai','Keskiviikko','Torstai','Perjantai','Lauantai'] as $i => $label)
                                <option value="{{ $i }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Alkaa</label>
                        <input type="time" name="start_time" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Päättyy</label>
                        <input type="time" name="end_time" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Lisää</button>
                </form>
            </div>

            <div class="mt-10 border-t pt-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Yksittäiset avaukset</h2>
                <p class="mt-1 text-xs text-gray-400">Avaa ylimääräinen yksittäinen päivä tälle hoidolle, viikkoaikataulun lisäksi.</p>

                <div class="mt-3 space-y-2">
                    @forelse ($treatment->specialOpenings as $opening)
                        <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2 text-sm">
                            <span>{{ \Illuminate\Support\Carbon::parse($opening->date)->translatedFormat('d.m.Y') }}, klo {{ substr($opening->start_time, 0, 5) }}–{{ substr($opening->end_time, 0, 5) }}</span>
                            <form method="POST" action="{{ route('ajanvaraus.openings.destroy', $opening) }}" onsubmit="return confirm('Poistetaanko tämä avaus?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">Poista</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Ei yksittäisiä avauksia.</p>
                    @endforelse
                </div>

                                @error('start_time')
                <p class="mt-2 text-sm text-red-600">{!! $message !!}</p>    
                @enderror

                <form method="POST" action="{{ route('ajanvaraus.openings.store', $treatment) }}" class="mt-4 flex items-end gap-2">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Päivä</label>
                        <input type="date" name="date" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Alkaa</label>
                        <input type="time" name="start_time" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Päättyy</label>
                        <input type="time" name="end_time" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Lisää</button>
                </form>
            </div>

            <div class="mt-10 border-t pt-6">
                <form method="POST" action="{{ route('ajanvaraus.treatments.destroy', $treatment) }}" onsubmit="return confirm('Poistetaanko hoito kokonaan? Tätä ei voi perua.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Poista hoito kokonaan</button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>