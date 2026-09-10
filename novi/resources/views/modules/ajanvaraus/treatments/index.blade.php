<x-app-layout>
    <div class="p-6 max-w-3xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Palvelut</h1>
            <a href="{{ route('ajanvaraus.treatments.create') }}" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">Lisää hoito</a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        @if (session('registration_error'))
            <div class="mt-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('registration_error') }}</div>
        @endif

        <div class="mt-4 rounded-lg border bg-white p-5">
            <h2 class="text-sm font-semibold text-gray-700">Lisää puhelimitse tullut varaus</h2>
            <p class="mt-1 text-xs text-gray-500">Valitse ensin palvelu hakeaksesi vapaat ajat.</p>

            <form method="GET" action="{{ route('ajanvaraus.treatments.index') }}" class="mt-3 flex gap-2">
                <select name="booking_treatment_id" class="w-full rounded-md border-gray-300 text-sm">
                    <option value="">Valitse palvelu</option>
                    @foreach ($treatments as $treatment)
                        <option value="{{ $treatment->id }}" @selected($selectedTreatment && $selectedTreatment->id === $treatment->id)>
                            {{ $treatment->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="shrink-0 rounded-md border px-4 py-2 text-sm font-semibold" style="border-color: var(--brand-secondary); color: var(--brand-text);">
                    Hae vapaat ajat
                </button>
            </form>

            @if ($selectedTreatment)
                @if (empty($dateSlots))
                    <p class="mt-3 text-sm text-gray-500">Ei vapaita aikoja lähimmän 30 päivän ajalta.</p>
                @else
                    <form method="POST" action="{{ route('ajanvaraus.appointments.store') }}" class="mt-4 space-y-3">
                        @csrf
                        <input type="hidden" name="treatment_id" value="{{ $selectedTreatment->id }}">
                        <div>
                            <label class="block text-sm font-medium">Aika</label>
                            <select name="starts_at" required class="mt-1 w-full rounded-md border-gray-300 text-sm"
                                onchange="document.getElementById('appointment-calendar-link').href = '{{ route('ajanvaraus.dashboard') }}?view=day&date=' + this.value.slice(0, 10);">
                                @foreach ($dateSlots as $date => $slots)
                                    @foreach ($slots as $slot)
                                        <option value="{{ $slot['iso'] }}">{{ $slot['label'] }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            <a id="appointment-calendar-link" href="{{ route('ajanvaraus.dashboard', ['view' => 'day', 'date' => array_key_first($dateSlots)]) }}" target="_blank" class="mt-1 inline-block text-sm underline" style="color: var(--brand-text);">Näytä aika kalenterissa</a>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium">Nimi</label>
                                <input type="text" name="name" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Sähköposti</label>
                                <input type="email" name="email" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Puhelin</label>
                                <input type="text" name="phone" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                        </div>
                        @if ($selectedTreatment->price > 0)
                            <div>
                                <label class="block text-sm font-medium mb-1">Maksutapa</label>
                                <div class="flex flex-col gap-2 text-sm">
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="payment_choice" value="send_link" checked>
                                        Lähetä maksulinkki sähköpostiin
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="payment_choice" value="pay_on_site">
                                        Maksaa paikan päällä
                                    </label>
                                </div>
                            </div>
                        @endif
                        <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-medium">Lisää varaus</button>
                    </form>
                @endif
            @endif
        </div>

        <div class="mt-6">
            <h2 class="text-sm font-semibold text-gray-700">Palveluiden hallinta</h2>
            <p class="mt-1 text-xs text-gray-400">Lisää ja muokkaa palveluita, ryhmittele ne oikeiden otsikoiden alle ja kirjoita palveluille kuvaukset. Tallennetut tiedot päivittyvät automaattisesti julkiselle palvelusivulle.</p>
        </div>

        @php
            $grouped = $treatments->groupBy('treatment_category_id');
        @endphp

        <div class="mt-4 space-y-6">
            @foreach ($categories as $category)
                @if ($grouped->has($category->id))
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $category->name }}</h3>
                        <div class="mt-2 space-y-3">
                            @foreach ($grouped[$category->id] as $treatment)
                                @include('ajanvaraus::treatments._row', ['treatment' => $treatment, 'categories' => $categories])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($grouped->has(''))
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Ei otsikkoa</h3>
                    <div class="mt-2 space-y-3">
                        @foreach ($grouped[''] as $treatment)
                            @include('ajanvaraus::treatments._row', ['treatment' => $treatment, 'categories' => $categories])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($treatments->isEmpty())
                <p class="text-sm text-gray-500">Ei vielä hoitoja. Lisää ensimmäinen hoito yllä olevasta napista.</p>
            @endif
        </div>

        <div class="mt-10 rounded-md border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-700">Palveluryhmien hallinta</h2>
            <p class="mt-1 text-xs text-gray-400">Hallinnoi palveluryhmien otsikoita, lyhyitä kuvauksia ja niiden järjestystä. Täällä tehdyt muutokset näkyvät julkisella palvelusivulla.</p>

            <div class="mt-3 space-y-2">
                @forelse ($categories as $category)
                    <div class="flex items-start gap-2">
                        <form method="POST" action="{{ route('ajanvaraus.categories.update', $category) }}" class="flex-1 flex flex-col gap-1">
                            @csrf @method('PATCH')
                            <div class="flex items-center gap-2">
                                <input type="number" name="order" value="{{ $category->order }}" min="0" class="w-14 rounded-md border-gray-300 shadow-sm text-sm" title="Järjestys">
                                <input type="text" name="name" value="{{ $category->name }}" class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                <button type="submit" class="text-xs text-gray-600 hover:text-gray-900 whitespace-nowrap">Tallenna</button>
                            </div>
                                                        <input type="text" name="description" value="{{ $category->description }}" placeholder="Lyhyt kuvaus (näkyy etusivulla)" maxlength="1000" class="rounded-md border-gray-300 shadow-sm text-xs text-gray-500">
                            @error('description')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <input type="text" name="link_url" value="{{ $category->link_url }}" placeholder="Linkki omalle sivulle (valinnainen, esim. /sydanpolku/tyohyvinvointipaivat)" class="rounded-md border-gray-300 shadow-sm text-xs text-gray-500">
                        </form>
                        <form method="POST" action="{{ route('ajanvaraus.categories.destroy', $category) }}" onsubmit="return confirm('Poistetaanko otsikko?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">Poista</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Ei vielä otsikoita.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('ajanvaraus.categories.store') }}" class="mt-4 flex items-end gap-2">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Uusi otsikko</label>
                    <input type="text" name="name" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                    <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Kuvaus (valinnainen)</label>
                    <input type="text" name="description" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Linkki (valinnainen)</label>
                    <input type="text" name="link_url" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div class="w-20">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Järj.</label>
                    <input type="number" name="order" value="0" min="0" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Lisää</button>
            </form>
        </div>
    </div>
</x-app-layout>