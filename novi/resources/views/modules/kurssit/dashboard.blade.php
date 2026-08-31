<x-app-layout>
    <div class="p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Etusivu</h1>
            <button type="button" onclick="window.location.href='{{ route('kurssit.courses.create') }}'"
                class="btn-brand rounded-md px-4 py-2 text-sm font-medium">
                + Uusi kurssi
            </button>
        </div>

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Tulevat kurssit</h2>

        <div class="mt-3 space-y-3">
            @forelse ($courses as $course)
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
                            @if ($course->isFull())
                                @if ($course->isTemporarilyFull())
                                    · <span class="text-amber-600 font-medium">Täynnä (odottaa maksuja)</span>
                                @else
                                    · <span class="text-red-600 font-medium">Täynnä</span>
                                @endif
                            @endif
                        </p>
                    </div>

                    <button type="button" onclick="window.location.href='{{ route('kurssit.courses.edit', $course) }}'"
                        class="text-sm text-gray-600 hover:text-gray-900">Muokkaa</button>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei tulevia kursseja.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>