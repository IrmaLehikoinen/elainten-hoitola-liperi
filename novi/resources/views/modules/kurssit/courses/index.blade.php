<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-semibold">Kurssit</h1>

                @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if (session('schedule_warning'))
            <div class="mt-4 rounded-md bg-orange-50 border border-orange-200 px-4 py-3 text-sm text-orange-800">
                {{ session('schedule_warning') }}
            </div>
        @endif

        <div class="mt-6 rounded-lg border bg-white p-5 max-w-2xl">
            <h2 class="text-sm font-semibold text-gray-700">Uusi kurssi</h2>
            @include('kurssit::courses._form', ['course' => $course])
        </div>

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Kaikki kurssit</h2>

        <div class="mt-3 space-y-3">
            @forelse ($courses as $course)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $course->name }}</p>
                        <p class="text-sm text-gray-500">
                            @if ($course->starts_at)
                                {{ $course->starts_at->format('d.m.Y H:i') }} ·
                            @endif
                            {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
                            · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
                            @if ($course->isFull())
                                · <span class="text-red-600 font-medium">Täynnä</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="window.location.href='{{ route('kurssit.courses.edit', $course) }}'"
                            class="text-sm text-gray-600 hover:text-gray-900">Muokkaa</button>

                        <form method="POST" action="{{ route('kurssit.courses.destroy', $course) }}"
                            onsubmit="return confirm('Poistetaanko kurssi {{ $course->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Poista</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä yhtään kurssia.</p>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            async function toJpegIfHeic(file) {
                const name = file.name.toLowerCase();
                const isHeic = file.type === 'image/heic' || file.type === 'image/heif'
                    || name.endsWith('.heic') || name.endsWith('.heif');

                if (!isHeic) {
                    return file;
                }

                try {
                    const converted = await heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
                    const blob = Array.isArray(converted) ? converted[0] : converted;
                    const newName = file.name.replace(/\.(heic|heif)$/i, '.jpg');
                    return new File([blob], newName, { type: 'image/jpeg' });
                } catch (e) {
                    console.error('HEIC-muunnos epäonnistui', e);
                    return file;
                }
            }

            async function compressImageFile(file, maxWidth, quality) {
                try {
                    file = await toJpegIfHeic(file);

                    if (!file.type.startsWith('image/') || file.type === 'image/gif') {
                        return file;
                    }

                    const bitmap = await createImageBitmap(file);
                    const scale = Math.min(1, maxWidth / bitmap.width);
                    const width = Math.round(bitmap.width * scale);
                    const height = Math.round(bitmap.height * scale);

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(bitmap, 0, 0, width, height);

                    const outputType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
                    const blob = await new Promise(function (resolve) {
                        canvas.toBlob(resolve, outputType, quality);
                    });

                    if (!blob || blob.size >= file.size) {
                        return file;
                    }

                    const newName = file.name.replace(/\.(png|jpe?g|heic|heif)$/i, outputType === 'image/png' ? '.png' : '.jpg');
                    return new File([blob], newName, { type: outputType });
                } catch (e) {
                    console.error('Kuvan pienennys epäonnistui, käytetään alkuperäistä tiedostoa', e);
                    return file;
                }
            }

            document.querySelectorAll('form').forEach(function (form) {
                let ready = false;

                form.addEventListener('submit', function (event) {
                    if (ready) {
                        return;
                    }

                    const fileInputs = Array.from(form.querySelectorAll('input[type="file"]'))
                        .filter(function (input) { return input.files && input.files[0]; });

                    if (fileInputs.length === 0) {
                        return;
                    }

                    event.preventDefault();

                    const submitButton = event.submitter;
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.dataset.originalText = submitButton.dataset.originalText || submitButton.textContent;
                        submitButton.textContent = 'Käsitellään kuvaa...';
                    }

                    Promise.all(fileInputs.map(async function (input) {
                        const original = input.files[0];
                        const compressed = await compressImageFile(original, 1600, 0.82);

                        if (compressed !== original) {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressed);
                            input.files = dataTransfer.files;
                        }
                    })).finally(function () {
                        ready = true;
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = submitButton.dataset.originalText;
                        }
                        if (form.requestSubmit) {
                            form.requestSubmit(submitButton || undefined);
                        } else {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>