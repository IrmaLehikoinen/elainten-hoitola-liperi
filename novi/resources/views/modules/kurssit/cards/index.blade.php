<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-semibold">Kurssikortit</h1>
        <p class="mt-1 text-sm text-gray-500">Klikkaa kurssia nähdäksesi kaikki tiedot ja osallistujat.</p>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if (session('registration_error'))
            <div class="mt-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                {{ session('registration_error') }}
            </div>
        @endif

        <div class="mt-4 rounded-lg border bg-white p-5">
            <h2 class="text-sm font-semibold text-gray-700">Lisää puhelimitse tullut varaus</h2>
            <p class="mt-1 text-xs text-gray-500">Käytä tätä kun asiakas ilmoittautuu kurssille puhelimessa tai paikan päällä.</p>

            <form method="POST" action="{{ route('kurssit.cards.store-registration-manual') }}" class="mt-3 space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Kurssi</label>
                    <select name="course_id" required class="mt-1 w-full rounded-md border-gray-300 text-sm"
                        onchange="var d = this.options[this.selectedIndex].dataset.date; document.getElementById('course-calendar-link').href = d ? ('{{ route('ajanvaraus.dashboard') }}?view=day&date=' + d) : '{{ route('ajanvaraus.dashboard') }}';">
                        <option value="">Valitse kurssi</option>
                        @foreach ($upcoming as $course)
                            <option value="{{ $course->id }}" data-date="{{ $course->starts_at?->format('Y-m-d') }}">
                                {{ $course->name }}@if ($course->starts_at) · {{ $course->starts_at->format('d.m.Y H:i') }} @endif
                            </option>
                        @endforeach
                    </select>
                    <a id="course-calendar-link" href="{{ route('ajanvaraus.dashboard') }}" target="_blank" class="mt-1 inline-block text-sm underline" style="color: var(--brand-text);">Näytä aika kalenterissa</a>
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
                <div>
                    <label class="block text-sm font-medium mb-1">Maksutapa</label>
                    <div class="flex flex-col gap-2 text-sm">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_choice" value="paid_now">
                            Maksettu heti (raha jo kädessä)
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_choice" value="send_link" checked>
                            Lähetä maksulinkki sähköpostiin
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_choice" value="pay_on_day">
                            Maksaa kurssipäivänä
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-medium">Lisää varaus</button>
            </form>
        </div>

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Tulevat kurssit</h2>
        <div class="mt-3 space-y-3">
            @forelse ($upcoming as $course)
                <div onclick="window.location.href='{{ route('kurssit.cards.show', $course) }}'"
                    class="cursor-pointer rounded-lg border bg-white p-4 flex items-center justify-between hover:border-gray-400">
                    <div>
                        <p class="font-medium">{{ $course->name }}</p>
                        <p class="text-sm text-gray-500">
                            @if ($course->starts_at)
                                {{ $course->starts_at->format('d.m.Y H:i') }} ·
                            @endif
                            {{ $course->confirmed_count }} / {{ $course->max_participants }} osallistujaa
                            @if ($course->isFull())
                                · <span class="text-red-600 font-medium">Täynnä</span>
                            @endif
                        </p>
                    </div>
                    <span class="text-sm text-gray-400">Avaa →</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei tulevia kursseja.</p>
            @endforelse
        </div>

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Menneet kurssit</h2>
        <div class="mt-3 space-y-3">
            @forelse ($past as $course)
                <div onclick="window.location.href='{{ route('kurssit.cards.show', $course) }}'"
                    class="cursor-pointer rounded-lg border bg-white p-4 flex items-center justify-between hover:border-gray-400">
                    <div>
                        <p class="font-medium">{{ $course->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $course->starts_at->format('d.m.Y H:i') }}
                            · {{ $course->confirmed_count }} / {{ $course->max_participants }} osallistujaa
                        </p>
                    </div>
                    <span class="text-sm text-gray-400">Avaa →</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä menneitä kursseja.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>