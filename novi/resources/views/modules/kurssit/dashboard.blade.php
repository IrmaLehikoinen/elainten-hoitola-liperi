<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-semibold">Etusivu</h1>

        <div class="mt-8 flex items-center justify-between gap-3">
            <h2 class="flex items-center gap-2 text-sm font-semibold text-gray-500 uppercase tracking-wide">
                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                    style="background-color: var(--brand-primary);"
                >
                    <svg viewBox="-1 -1 32 50" fill="none" stroke="white" stroke-width="1.1" class="h-5 w-5">
                        <path stroke-linecap="round" d="M15 48V34" />
                        <path stroke-linejoin="round" d="M15 34C10 34 0 29 0 20C8 20 15 27 15 34Z" />
                        <path stroke-linejoin="round" d="M15 34C20 34 30 29 30 20C22 20 15 27 15 34Z" />
                        <path stroke-linejoin="round" d="M15 28C8 23 5 11 15 0C25 11 22 23 15 28Z" />
                    </svg>
                </span>
                {{ ucfirst($calendarMonth->translatedFormat('F')) }} — tulevat kurssit
            </h2>

            <div class="flex shrink-0 gap-2">
                <a href="{{ route('kurssit.cards.index') }}" class="rounded-md border px-3 py-1 text-xs font-semibold" style="border-color: var(--brand-secondary); color: var(--brand-text);">+ Kurssivaraus</a>
                <a href="{{ route('ajanvaraus.treatments.index') }}" class="rounded-md border px-3 py-1 text-xs font-semibold" style="border-color: var(--brand-secondary); color: var(--brand-text);">+ Palveluvaraus</a>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap gap-3">
            @forelse ($thisMonthCourses as $course)
                <div onclick="window.location.href='{{ route('kurssit.courses.edit', $course) }}'"
                    class="w-56 cursor-pointer rounded-lg border bg-white p-3 transition hover:shadow-md">
                    <p class="text-sm font-medium">{{ $course->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        @if ($course->starts_at)
                            {{ $course->starts_at->format('d.m.Y H:i') }}
                        @else
                            Ei ajankohtaa
                        @endif
                        · {{ $course->remainingSpots() }} / {{ $course->max_participants }} vapaana
                    </p>
                    <p class="mt-2 text-xs text-gray-600">Muokkaa →</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei kursseja tässä kuussa.</p>
            @endforelse
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-4">
            <section class="rounded-xl border bg-white p-5 md:col-span-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="window.location.href='{{ route('kurssit.dashboard', ['date' => $calendarMonth->copy()->subMonth()->format('Y-m-d')]) }}'"
                            class="flex h-8 w-8 items-center justify-center rounded-md border text-gray-600">←</button>

                        <h2 class="text-base font-semibold">{{ ucfirst($calendarMonth->translatedFormat('F Y')) }}</h2>

                        <button type="button" onclick="window.location.href='{{ route('kurssit.dashboard', ['date' => $calendarMonth->copy()->addMonth()->format('Y-m-d')]) }}'"
                            class="flex h-8 w-8 items-center justify-center rounded-md border text-gray-600">→</button>

                        @if (!$calendarMonth->isSameMonth(today()))
                            <button type="button" onclick="window.location.href='{{ route('kurssit.dashboard') }}'"
                                class="rounded-md border px-2 py-1 text-xs">Tänään</button>
                        @endif
                    </div>

                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                        style="background-color: var(--brand-secondary);"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="1.8" class="h-4 w-4">
                            <rect x="3.5" y="5" width="17" height="16" rx="2" />
                            <path stroke-linecap="round" d="M3.5 9.5h17M8 3v4M16 3v4" />
                        </svg>
                    </span>
                </div>

                <div class="mt-4">
                 @include('partials.month-calendar', ['days' => $calendarDays, 'dayRoute' => 'ajanvaraus.calendar.day'])  
                </div>
            </section>

            <div class="space-y-6">
            <section class="rounded-xl border bg-white p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold">Muistettavaa</h2>

                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                        style="background-color: var(--brand-secondary);"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="1.8" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.2M18.5 9.5c0-3.6-2.9-5.5-6.5-5.5s-6.5 1.9-6.5 5.5c0 5-2 6.5-2 6.5h17s-2-1.5-2-6.5ZM9.5 19a2.5 2.5 0 0 0 5 0" />
                        </svg>
                    </span>
                </div>

                <div class="mt-3 space-y-2">
                    @forelse ($reminders as $reminder)
                        <div class="flex items-start gap-2 rounded-md border p-2" data-reminder-id="{{ $reminder['id'] }}">
                            <button type="button" onclick="toggleReminder({{ $reminder['id'] }}, this)"
                                class="mt-0.5 h-4 w-4 shrink-0 rounded border border-gray-400"></button>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm">{{ $reminder['title'] }}</p>
                                <p class="text-xs text-gray-500">
                                    @if ($reminder['due_at']) {{ $reminder['due_at'] }} @endif
                                    @if ($reminder['course_label'])
                                        · <span class="font-medium">{{ $reminder['course_label'] }}</span>
                                    @endif
                                </p>
                            </div>
                            <button type="button" onclick="deleteReminder({{ $reminder['id'] }}, this)"
                                class="text-xs text-red-500 hover:text-red-700">✕</button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Ei avoimia muistutuksia.</p>
                    @endforelse
                </div>

                <button type="button" onclick="document.getElementById('reminder-form').classList.toggle('hidden')"
                    class="mt-3 text-sm text-gray-600 hover:text-gray-900">+ Lisää muistutus</button>

                <form id="reminder-form" method="POST" action="{{ route('kurssit.reminders.store') }}" class="mt-3 hidden space-y-2">
                    @csrf
                    <input type="text" name="title" placeholder="Esim. Tilaa materiaalit" required class="w-full rounded-md border-gray-300 text-sm">
                    <input type="date" name="due_at" class="w-full rounded-md border-gray-300 text-sm">
                    <select name="course_id" class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Ei liity tiettyyn kurssiin</option>
                        @foreach ($thisMonthCourses->concat($otherUpcomingCourses) as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}@if($c->starts_at) — {{ $c->starts_at->format('d.m.Y') }}@endif</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-brand rounded-md px-3 py-1.5 text-sm">Tallenna muistutus</button>
                </form>
            </section>

            <section class="rounded-xl border bg-white p-5">
                <h2 class="text-base font-semibold">Muistutukset (viikon sisällä)</h2>

                <div class="mt-3 space-y-2" x-data="{ removed: {} }">
                    @forelse ($upcomingReminders as $entry)
                        <div
                            class="flex items-start gap-3 rounded-md border p-2"
                            x-show="!removed['{{ $entry['type'] }}_{{ $entry['id'] }}']"
                            x-cloak
                        >
                            <button
                                type="button"
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border border-gray-300"
                                aria-label="Merkitse tehdyksi"
                                @click="
                                    fetch('{{ route('kurssit.reminders.dismiss') }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                        body: JSON.stringify({ type: '{{ $entry['type'] }}', id: {{ $entry['id'] }} }),
                                    }).then(() => { removed['{{ $entry['type'] }}_{{ $entry['id'] }}'] = true });
                                "
                            ></button>

                            <a href="{{ $entry['edit_url'] }}" class="min-w-0 flex-1">
                                <p class="text-sm font-medium">{{ $entry['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $entry['date']->translatedFormat('d.m.Y') }}</p>
                                <p class="mt-1 text-sm">{{ $entry['note'] }}</p>
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Ei muistutuksia tulevalle viikolle.</p>
                    @endforelse
                </div>
            </section>
            </div>
        </div>

        <div class="mt-6 text-center">
            <button type="button" onclick="openNewCourseForm()" class="btn-brand rounded-md px-6 py-3 text-base font-semibold">
                + Uusi kurssi
            </button>
        </div>

        <div id="new-course-form" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/40 p-6" onclick="if (event.target === this) closeNewCourseForm();">
            <div class="mx-auto mt-10 w-full max-w-2xl rounded-lg border bg-white p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">Uusi kurssi</h2>
                    <button type="button" onclick="closeNewCourseForm()" class="text-gray-400 hover:text-gray-700">✕</button>
                </div>
                @include('kurssit::courses._form', ['course' => $course, 'ignoreOld' => true])
            </div>
        </div>

        <h2 class="mt-10 flex items-center gap-2 text-sm font-semibold text-gray-500 uppercase tracking-wide">
            <span
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                style="background-color: var(--brand-primary);"
            >
                <svg viewBox="-1 -1 32 50" fill="none" stroke="white" stroke-width="1.1" class="h-5 w-5">
                    <path stroke-linecap="round" d="M15 48V34" />
                    <path stroke-linejoin="round" d="M15 34C10 34 0 29 0 20C8 20 15 27 15 34Z" />
                    <path stroke-linejoin="round" d="M15 34C20 34 30 29 30 20C22 20 15 27 15 34Z" />
                    <path stroke-linejoin="round" d="M15 28C8 23 5 11 15 0C25 11 22 23 15 28Z" />
                </svg>
            </span>
            Muut tulevat kurssit
        </h2>
        <div class="mt-3 space-y-3">
            @forelse ($otherUpcomingCourses as $course)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $course->name }}</p>
                        <p class="text-sm text-gray-500">
                            @if ($course->starts_at)
                                {{ $course->starts_at->format('d.m.Y H:i') }} ·
                            @else
                                Ei ajankohtaa ·
                            @endif
                            {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
                            · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
                        </p>
                    </div>
                    <button type="button" onclick="window.location.href='{{ route('kurssit.courses.edit', $course) }}'"
                        class="text-sm text-gray-600 hover:text-gray-900">Muokkaa</button>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei muita tulevia kursseja.</p>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
            function openNewCourseForm() {
            document.getElementById('new-course-form').classList.remove('hidden');
        }

        function closeNewCourseForm() {
            document.getElementById('new-course-form').classList.add('hidden');
        }

            function pickDay(dateStr, courseId) {
            if (courseId) {
                window.location.href = '/kurssit/hallinta/kurssikortit/' + courseId;
                return;
            }
            openNewCourseForm();
            const input = document.getElementById('course-starts-at');
            if (input) { input.value = dateStr + 'T10:00'; }
        }

        async function toggleReminder(id, btn) {
            const response = await fetch(`/kurssit/hallinta/muistutukset/${id}/vaihda`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });
            const data = await response.json();
            if (data.done) {
                btn.closest('[data-reminder-id]').remove();
            }
        }

        async function deleteReminder(id, btn) {
            await fetch(`/kurssit/hallinta/muistutukset/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });
            btn.closest('[data-reminder-id]').remove();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            async function toJpegIfHeic(file) {
                const name = file.name.toLowerCase();
                const isHeic = file.type === 'image/heic' || file.type === 'image/heif'
                    || name.endsWith('.heic') || name.endsWith('.heif');
                if (!isHeic) { return file; }
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
                    if (!file.type.startsWith('image/') || file.type === 'image/gif') { return file; }
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
                    if (!blob || blob.size >= file.size) { return file; }
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
                    if (ready) { return; }
                    const fileInputs = Array.from(form.querySelectorAll('input[type="file"]'))
                        .filter(function (input) { return input.files && input.files[0]; });
                    if (fileInputs.length === 0) { return; }
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