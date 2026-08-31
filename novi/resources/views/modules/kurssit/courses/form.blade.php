<x-app-layout>
    <div class="p-6 max-w-2xl" x-data="{ presentationType: '{{ old('presentation_type', $course->presentation_type ?? 'text') }}' }">
        <h1 class="text-xl font-semibold">{{ $course->exists ? 'Muokkaa kurssia' : 'Uusi kurssi' }}</h1>

        <form
            method="POST"
            action="{{ $course->exists ? route('kurssit.courses.update', $course) : route('kurssit.courses.store') }}"
            enctype="multipart/form-data"
            class="mt-6 space-y-5"
        >
            @csrf
            @if ($course->exists)
                @method('PATCH')
            @endif

            <div>
                <label class="block text-sm font-medium">Kurssin nimi</label>
                <input type="text" name="name" value="{{ old('name', $course->name) }}" required class="mt-1 w-full rounded-md border-gray-300">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Ajankohta</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $course->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium">Hinta (€)</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $course->price) }}" class="mt-1 w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium">Paikkamäärä</label>
                    <input type="number" min="0" name="max_participants" value="{{ old('max_participants', $course->max_participants) }}" required class="mt-1 w-full rounded-md border-gray-300">
                    @error('max_participants') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Esittelytapa</label>
                <div class="mt-2 flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="presentation_type" value="text" x-model="presentationType">
                        Kirjoita itse
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="presentation_type" value="brochure" x-model="presentationType">
                        Lataa valmis esite
                    </label>
                </div>
            </div>

            <div x-show="presentationType === 'text'">
                <label class="block text-sm font-medium">Kuvaus</label>
                <input id="description_html" type="hidden" name="description_html" value="{{ old('description_html', $course->description_html) }}">
                <trix-editor input="description_html" class="mt-1 block w-full rounded-md border-gray-300"></trix-editor>
            </div>

            <div x-show="presentationType === 'brochure'">
                <label class="block text-sm font-medium">Esite (PDF tai kuva)</label>
                <input type="file" name="brochure" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 w-full">
                @if ($course->brochure_path)
                    <p class="mt-1 text-sm text-gray-500">Nykyinen esite: {{ basename($course->brochure_path) }} (lataa uusi korvataksesi)</p>
                @endif
                @error('brochure') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-md bg-[var(--brand-primary)] px-4 py-2 text-sm font-medium text-white">Tallenna</button>
                <button type="button" onclick="window.location.href='{{ route('kurssit.courses.index') }}'" class="rounded-md border px-4 py-2 text-sm">Peruuta</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script type="module" src="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.css">
    @endpush
</x-app-layout>