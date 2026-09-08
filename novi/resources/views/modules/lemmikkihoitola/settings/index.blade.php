<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                Yritysasetukset
            </h1>

            <p class="mt-1 text-sm text-gray-500">
             Lemmikkiryhmät, palvelut, muistutustyypit ja hoitomuodot  
            </p>
        </div>
    </x-slot>

        <div class="py-8" x-data="{ activeTab: '{{ request('tab', 'perushinta') }}' }" @submit="$event.target.action = $event.target.action.split('?')[0] + '?tab=' + activeTab">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm font-medium text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Välilehtinavigaatio --}}
            <div class="flex flex-wrap gap-2 rounded-lg bg-white p-3 shadow-sm">
                <button
                    type="button"
                    @click="activeTab = 'perushinta'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                    :style="activeTab === 'perushinta' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Perushinta
                </button>

                                <button
                    type="button"
                    @click="activeTab = 'varausmaksu'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                    :style="activeTab === 'varausmaksu' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Varausmaksu
                </button>

                <button
                    type="button"
                    @click="activeTab = 'lomake'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                    :style="activeTab === 'lomake' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Ajanvarauslomake
                </button>

                <button
                    type="button"
                    @click="activeTab = 'elainryhmat'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                                        :style="activeTab === 'elainryhmat' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Lemmikkiryhmät ja kapasiteetti
                </button>

                <button
                    type="button"
                    @click="activeTab = 'palvelut'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                    :style="activeTab === 'palvelut' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Lisäpalvelut
                </button>

                <button
                    type="button"
                    @click="activeTab = 'muistutustyypit'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                    :style="activeTab === 'muistutustyypit' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Muistutustyypit
                </button>

                <button
                    type="button"
                    @click="activeTab = 'hoitomuodot'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                    :style="activeTab === 'hoitomuodot' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Hoitomuodot
                </button>

                <button
                    type="button"
                    @click="activeTab = 'yritystiedot'"
                    class="rounded-md px-4 py-2 text-sm font-medium"
                    :style="activeTab === 'yritystiedot' ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'color: var(--brand-text);'"
                >
                    Yritystiedot
                </button>
            </div>

            {{-- Perushinta --}}
            <section x-show="activeTab === 'perushinta'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Perushinta
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                 Hoitopäivän hinta per lemmikki. Käytetään varauksen kokonaishinnan ja ennakkomaksun laskemiseen.   
                </p>

                <form method="POST" action="{{ route('admin.settings.base-rate.update') }}" class="mt-4 flex items-center gap-3">
                    @csrf
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="base_daily_rate"
                        value="{{ $company->settings['base_daily_rate'] ?? 0 }}"
                        class="w-32 rounded-md border-gray-300 shadow-sm"
                    >
                    <span class="text-sm text-gray-500">€ / hoitopäivä / lemmikki</span>

                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                        Tallenna
                    </button>
                </form>
            </section>

            {{-- Varausmaksu --}}
            <section x-show="activeTab === 'varausmaksu'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Varausmaksu
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kuinka suuri osuus hinnasta peritään ennakkomaksuna varausta tehdessä. 0 % = ei ennakkomaksua, varaus vahvistuu heti.
                </p>

                <form method="POST" action="{{ route('admin.settings.deposit.update') }}" class="mt-4 flex items-center gap-3">
                    @csrf
                    <select name="deposit_percentage" class="rounded-md border-gray-300 shadow-sm">
                        @foreach ([0, 20, 30, 50] as $percentage)
                            <option value="{{ $percentage }}" @selected(($company->settings['deposit_percentage'] ?? 0) == $percentage)>
                                {{ $percentage }} %
                            </option>
                        @endforeach
                    </select>

                                <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                        Tallenna
                    </button>
                </form>
            </section>

            {{-- Ajanvarauslomake --}}
            <section x-show="activeTab === 'lomake'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Ajanvarauslomake
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Valitse mitkä lisätiedot kysytään lemmikistä julkisella ajanvarauslomakkeella. Nimi ja laji kysytään aina.
                </p>

                <form method="POST" action="{{ route('admin.settings.booking-fields.update') }}" class="mt-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($bookingFields as $key => $field)
                            <label class="flex items-center gap-2 rounded-md border p-3" style="border-color: var(--brand-secondary);">
                                <input
                                    type="checkbox"
                                    name="fields[]"
                                    value="{{ $key }}"
                                    @checked(in_array($key, $enabledBookingFields))
                                                                     class="rounded border-gray-300"
                                    style="accent-color: var(--brand-primary); color: var(--brand-primary);"   
                                >
                                <span class="text-sm" style="color: var(--brand-text);">{{ $field['label'] }}</span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold mt-4">
                        Tallenna
                    </button>
                </form>
            </section>

                        {{-- Lemmikkiryhmät ja kapasiteetti --}}
            <section x-show="activeTab === 'elainryhmat'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Lemmikkiryhmät ja kapasiteetti
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kuinka monta kutakin lajia voi olla hoidossa samaan aikaan.
                </p>

                <div class="mt-4 space-y-3">
                    @foreach ($resources as $resource)
                        <form method="POST" action="{{ route('admin.settings.resources.update', $resource) }}" class="flex flex-wrap items-end gap-3 rounded-md border p-3" style="border-color: var(--brand-secondary);">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                                <input type="text" name="name" value="{{ $resource->name }}" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Laji</label>
                                <input type="text" name="type" value="{{ $resource->type }}" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Kapasiteetti</label>
                                <input type="number" min="0" name="capacity" value="{{ $resource->capacity }}" class="mt-1 w-24 rounded-md border-gray-300 shadow-sm">
                            </div>

                            <button type="submit" class="btn-brand rounded-md px-3 py-2 text-sm font-semibold">Tallenna</button>
                        </form>

                        <form method="POST" action="{{ route('admin.settings.resources.destroy', $resource) }}" onsubmit="return confirm('Poistetaanko {{ $resource->name }}?');" class="-mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600">Poista {{ $resource->name }}</button>
                        </form>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.settings.resources.store') }}" class="mt-6 flex flex-wrap items-end gap-3 border-t pt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                        <input type="text" name="name" placeholder="Esim. Koirapaikat" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Laji</label>
                        <input type="text" name="type" placeholder="Esim. koira" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Kapasiteetti</label>
                        <input type="number" min="0" name="capacity" value="1" class="mt-1 w-24 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">Lisää lemmikkiryhmä</button>
                </form>
            </section>

            {{-- Lisäpalvelut --}}
            <section x-show="activeTab === 'palvelut'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Lisäpalvelut, hinnat ja tuotteet
                </h2>

                <div class="mt-4 space-y-3">
                    @foreach ($services as $service)
                        <form method="POST" action="{{ route('admin.settings.services.update', $service) }}" class="flex flex-wrap items-end gap-3 rounded-md border p-3" style="border-color: var(--brand-secondary);">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                                <input type="text" name="name" value="{{ $service->name }}" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Kuvaus</label>
                                <input type="text" name="description" value="{{ $service->description }}" class="mt-1 w-48 rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hinta (€)</label>
                                <input type="number" step="0.01" min="0" name="price" value="{{ $service->price }}" class="mt-1 w-28 rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hinnoittelu</label>
                                <select name="pricing_type" class="mt-1 w-36 rounded-md border-gray-300 shadow-sm">
                                    <option value="per_day" @selected($service->pricing_type === 'per_day')>Per päivä</option>
                                    <option value="per_booking" @selected($service->pricing_type === 'per_booking')>Per varaus</option>
                                    <option value="fixed" @selected($service->pricing_type === 'fixed')>Kiinteä</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-brand rounded-md px-3 py-2 text-sm font-semibold">Tallenna</button>
                        </form>

                        <form method="POST" action="{{ route('admin.settings.services.destroy', $service) }}" onsubmit="return confirm('Poistetaanko {{ $service->name }}?');" class="-mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600">Poista {{ $service->name }}</button>
                        </form>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.settings.services.store') }}" class="mt-6 flex flex-wrap items-end gap-3 border-t pt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                        <input type="text" name="name" placeholder="Esim. Kylvetys" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Kuvaus</label>
                        <input type="text" name="description" class="mt-1 w-48 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hinta (€)</label>
                        <input type="number" step="0.01" min="0" name="price" value="0" class="mt-1 w-28 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hinnoittelu</label>
                        <select name="pricing_type" class="mt-1 w-36 rounded-md border-gray-300 shadow-sm">
                            <option value="per_day">Per päivä</option>
                            <option value="per_booking">Per varaus</option>
                            <option value="fixed">Kiinteä</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">Lisää palvelu</button>
                </form>
            </section>

            {{-- Muistutustyypit --}}
            <section x-show="activeTab === 'muistutustyypit'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Muistutustyypit
                </h2>

                <div class="mt-4 space-y-3">
                    @foreach ($reminderTypes as $type)
                        <form method="POST" action="{{ route('admin.settings.reminder-types.update', $type) }}" class="flex flex-wrap items-end gap-3 rounded-md border p-3" style="border-color: var(--brand-secondary);">
                            @csrf
                            @method('PATCH') 
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                                <input type="text" name="label" value="{{ $type->label }}" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Järjestys</label>
                                <input type="number" name="sort_order" value="{{ $type->sort_order }}" class="mt-1 w-20 rounded-md border-gray-300 shadow-sm">
                            </div>

                            <button type="submit" class="btn-brand rounded-md px-3 py-2 text-sm font-semibold">Tallenna</button>
                        </form>

                        <form method="POST" action="{{ route('admin.settings.reminder-types.destroy', $type) }}" onsubmit="return confirm('Poistetaanko {{ $type->label }}?');" class="-mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600">Poista {{ $type->label }}</button>
                        </form>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.settings.reminder-types.store') }}" class="mt-6 flex flex-wrap items-end gap-3 border-t pt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Tunniste (esim. "trimmaus")</label>
                        <input type="text" name="slug" placeholder="esim_trimmaus" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                        <input type="text" name="label" placeholder="Esim. Trimmaus" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Järjestys</label>
                        <input type="number" name="sort_order" value="0" class="mt-1 w-20 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">Lisää muistutustyyppi</button>
                </form>
            </section>

            {{-- Hoitomuodot --}}
            <section x-show="activeTab === 'hoitomuodot'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Hoitomuodot
                </h2>

                <div class="mt-4 space-y-3">
                    @foreach ($careTypes as $type)
                        <form method="POST" action="{{ route('admin.settings.care-types.update', $type) }}" class="flex flex-wrap items-end gap-3 rounded-md border p-3" style="border-color: var(--brand-secondary);">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                                <input type="text" name="label" value="{{ $type->label }}" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Järjestys</label>
                                <input type="number" name="sort_order" value="{{ $type->sort_order }}" class="mt-1 w-20 rounded-md border-gray-300 shadow-sm">
                            </div>

                            <button type="submit" class="btn-brand rounded-md px-3 py-2 text-sm font-semibold">Tallenna</button>
                        </form>

                        <form method="POST" action="{{ route('admin.settings.care-types.destroy', $type) }}" onsubmit="return confirm('Poistetaanko {{ $type->label }}?');" class="-mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600">Poista {{ $type->label }}</button>
                        </form>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.settings.care-types.store') }}" class="mt-6 flex flex-wrap items-end gap-3 border-t pt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Tunniste (esim. "paivahoito")</label>
                        <input type="text" name="slug" placeholder="esim_paivahoito" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Nimi</label>
                        <input type="text" name="label" placeholder="Esim. Päivähoito" class="mt-1 w-40 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Järjestys</label>
                        <input type="number" name="sort_order" value="0" class="mt-1 w-20 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">Lisää hoitomuoto</button>
                </form>
            </section>

            {{-- Yritystiedot --}}
            <section x-show="activeTab === 'yritystiedot'" x-cloak class="bg-white p-6 shadow-sm rounded-lg">
                <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
                    Yritystiedot
                </h2>
                                        <p class="mt-1 text-sm text-gray-500">
                    Nämä tiedot näkyvät asiakkaille lähetettävillä kuiteilla.
                </p>

                <p class="mt-3 text-sm">
                    <span class="font-semibold" style="color: var(--brand-text);">Toimiala:</span>
                    <span style="color: var(--brand-text);">{{ config('industries.' . $company->industry . '.label', $company->industry) }}</span>
                </p>

                    <form method="POST" action="{{ route('admin.settings.company-info.update') }}" class="mt-4 space-y-4 max-w-md" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Logo</label>
                        @if ($company->logo_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($company->logo_path) }}" class="mt-1 mb-2 h-12 w-auto object-contain">
                        @endif
                        <input type="file" name="logo" accept="image/*" class="mt-1 w-full text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Virallinen nimi</label>
                        <input
                            type="text"
                            name="official_name"
                            value="{{ $company->settings['official_name'] ?? '' }}"
                            placeholder="Esim. Liperin Eläinhoitola Oy"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Puhelin</label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ $company->phone }}"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Sähköposti</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ $company->email }}"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Y-tunnus</label>
                        <input
                            type="text"
                            name="business_id"
                            value="{{ $company->settings['business_id'] ?? '' }}"
                            placeholder="1234567-8"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                                            <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Osoite</label>
                        <input
                            type="text"
                            name="address"
                            value="{{ $company->settings['address'] ?? '' }}"
                            placeholder="Esim. Kelotie 5, 83100 Liperi"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Facebook-osoite</label>
                        <input
                            type="url"
                            name="facebook_url"
                            value="{{ $company->settings['facebook_url'] ?? '' }}"
                            placeholder="https://facebook.com/yrityksesi"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Instagram-osoite</label>
                        <input
                            type="url"
                            name="instagram_url"
                            value="{{ $company->settings['instagram_url'] ?? '' }}"
                            placeholder="https://instagram.com/yrityksesi"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Pankkitili (IBAN)</label>
                        <input
                            type="text"
                            name="iban"
                            value="{{ $company->settings['iban'] ?? '' }}"
                            placeholder="FI12 3456 7890 1234 56"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Maksuehto (pv netto)</label>
                        <input
                            type="number"
                            min="1"
                            max="90"
                            name="payment_term_days"
                            value="{{ $company->settings['payment_term_days'] ?? 14 }}"
                            class="mt-1 w-32 rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                                 <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">ALV-prosentti</label>
                        <input
                            type="number"
                            step="0.1"
                            min="0"
                            max="100"
                            name="vat_percentage"
                            value="{{ $company->settings['vat_percentage'] ?? 25.5 }}"
                            class="mt-1 w-32 rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                                        <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hoitosopimus (teksti)</label>
                        <textarea
                            name="care_contract_text"
                            rows="8"
                            placeholder="Liitä tähän hoitosopimuksen teksti. Näytetään asiakkaalle varauksen yhteydessä."
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm"
                        >{{ $company->settings['care_contract_text'] ?? '' }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Tätä tekstiä käytetään jatkossa varaussivun hoitosopimuksessa.</p>

                        <label class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                            <input
                                type="checkbox"
                                name="care_contract_enabled"
                                value="1"
                                @checked($company->settings['care_contract_enabled'] ?? true)
                                class="rounded border-gray-300"
                            >
                            Näytä hoitosopimus ja vaadi sen hyväksyntä varauksen viimeisellä sivulla
                        </label>
                        <p class="mt-1 text-xs text-gray-400">Jos et halua hoitosopimusta käyttöön, poista rasti — teksti säilyy tallessa, mutta sitä ei näytetä eikä vaadita asiakkaalta.</p>
                    </div>

                    <div class="flex gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Pääväri</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input
                                    type="color"
                                    id="primary_color_picker"
                                    value="{{ $company->primary_color ?? '#3F4F3A' }}"
                                    class="h-10 w-16 rounded-md border-gray-300 shadow-sm"
                                    oninput="document.getElementById('primary_color_text').value = this.value"
                                >
                                <input
                                    type="text"
                                    name="primary_color"
                                    id="primary_color_text"
                                    value="{{ $company->primary_color ?? '#3F4F3A' }}"
                                    placeholder="#3F4F3A"
                                    class="w-28 rounded-md border-gray-300 shadow-sm text-sm"
                                    oninput="if (/^#([0-9A-Fa-f]{6})$/.test(this.value)) { document.getElementById('primary_color_picker').value = this.value; }"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Toissijainen väri</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input
                                    type="color"
                                    id="secondary_color_picker"
                                    value="{{ $company->secondary_color ?? '#D8C6BD' }}"
                                    class="h-10 w-16 rounded-md border-gray-300 shadow-sm"
                                    oninput="document.getElementById('secondary_color_text').value = this.value"
                                >
                                <input
                                    type="text"
                                    name="secondary_color"
                                    id="secondary_color_text"
                                    value="{{ $company->secondary_color ?? '#D8C6BD' }}"
                                    placeholder="#D8C6BD"
                                    class="w-28 rounded-md border-gray-300 shadow-sm text-sm"
                                    oninput="if (/^#([0-9A-Fa-f]{6})$/.test(this.value)) { document.getElementById('secondary_color_picker').value = this.value; }"
                                >
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Oletuksena Novin vihreä (#3F4F3A) ja beige (#D8C6BD). Vaihda nämä oman yrityksesi brändiväreihin — niitä käytetään kuiteissa, laskuissa ja jatkossa julkisella ajanvarausivulla, ei hallintapaneelissa.</p>

                    <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">
                        Tallenna
                    </button>
                </form>       
            </section>

        </div>
    </div>
</x-app-layout>