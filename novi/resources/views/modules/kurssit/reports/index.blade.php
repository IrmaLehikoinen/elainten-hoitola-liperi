<x-app-layout>
    <div class="p-6 max-w-3xl">
        <h1 class="text-xl font-semibold">Raportti</h1>
        <p class="mt-1 text-sm text-gray-500">Menneet kurssit ja niiden osallistujamäärät.</p>

        <form method="GET" action="{{ route('kurssit.reports.index') }}" class="mt-4 flex gap-2">
            <input type="hidden" name="participant" value="{{ $participantSearch }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Hae kurssin nimellä"
                class="w-full rounded-md border-gray-300 text-sm">
            <button type="submit" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-medium">Hae</button>
        </form>

        <div class="mt-6 space-y-3">
            @forelse ($courses as $course)
                <div onclick="window.location.href='{{ route('kurssit.cards.show', $course) }}'"
                    class="cursor-pointer rounded-lg border bg-white p-4 flex items-center justify-between hover:border-gray-400">
                    <div>
                        <p class="font-medium">{{ $course->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $course->starts_at?->format('d.m.Y H:i') }}
                            · {{ $course->confirmed_count }} / {{ $course->max_participants }} osallistujaa
                        </p>
                    </div>
                    <p class="text-sm font-medium text-gray-700">
                        {{ number_format($course->confirmed_count * $course->price, 2, ',', ' ') }} €
                    </p>
                </div>
            @empty
                @if ($search !== '')
                    <p class="text-sm text-gray-500">Ei tuloksia haulla "{{ $search }}".</p>
                @else
                    <p class="text-sm text-gray-500">Hae kurssin nimellä nähdäksesi menneet kurssit.</p>
                @endif
            @endforelse
        </div>

        <h2 class="mt-10 text-sm font-semibold text-gray-500 uppercase tracking-wide">Hae osallistujaa</h2>
        <p class="mt-1 text-sm text-gray-500">Löytää kaikki kurssit joihin henkilö on ilmoittautunut, myös tulevat.</p>

        <form method="GET" action="{{ route('kurssit.reports.index') }}" class="mt-4 flex gap-2">
            <input type="hidden" name="q" value="{{ $search }}">
            <input type="text" name="participant" value="{{ $participantSearch }}" placeholder="Hae nimellä"
                class="w-full rounded-md border-gray-300 text-sm">
            <button type="submit" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-medium">Hae</button>
        </form>

        <div class="mt-4 space-y-2">
            @forelse ($participantRegistrations as $registration)
                <div onclick="window.location.href='{{ route('kurssit.registrations.show', $registration) }}'"
                    class="cursor-pointer rounded-lg border bg-white p-4 flex items-center justify-between hover:border-gray-400">
                    <div>
                        <p class="font-medium">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $registration->course->name }}
                            @if ($registration->course->starts_at)
                                · {{ $registration->course->starts_at->format('d.m.Y') }}
                            @endif
                            ·
                            @if ($registration->status === 'confirmed')
                                <span class="text-green-700">Maksettu</span>
                            @elseif ($registration->status === 'cancelled')
                                <span class="text-gray-400">Peruttu</span>
                            @else
                                <span class="text-amber-600">Odottaa maksua</span>
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                @if ($participantSearch !== '')
                    <p class="text-sm text-gray-500">Ei tuloksia haulla "{{ $participantSearch }}".</p>
                @endif
            @endforelse
        </div>
    </div>
</x-app-layout>