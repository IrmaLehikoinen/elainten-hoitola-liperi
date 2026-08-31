<x-app-layout>
    <div class="p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Kurssit</h1>
            <button type="button" onclick="window.location.href='{{ route('kurssit.courses.create') }}'"
                class="rounded-md bg-[var(--brand-primary)] px-4 py-2 text-sm font-medium text-white">
                + Uusi kurssi
            </button>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 space-y-3">
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
</x-app-layout>