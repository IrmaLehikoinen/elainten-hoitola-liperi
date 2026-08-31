<x-app-layout>
    <div class="p-6 max-w-4xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Kurssit</h1>
            <button onclick="window.location.href='{{ route('kurssit.courses.create') }}'" class="rounded-md bg-[var(--brand-primary)] px-4 py-2 text-sm font-medium text-white">
                + Uusi kurssi
            </button>
        </div>

        @if (session('status'))
            <p class="mt-4 rounded-md bg-green-50 px-4 py-2 text-sm text-green-700">{{ session('status') }}</p>
        @endif

        <div class="mt-6 space-y-3">
            @forelse ($courses as $course)
                <div class="rounded-lg border bg-white p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ $course->name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $course->starts_at?->format('d.m.Y H:i') ?? 'Ei ajankohtaa' }}
                                · {{ number_format($course->price, 2) }} €
                                · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
                                @if ($course->isFull())
                                    <span class="font-semibold text-red-600">— Täynnä</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="window.location.href='{{ route('kurssit.courses.edit', $course) }}'" class="text-sm text-gray-600 underline">Muokkaa</button>
                            <form method="POST" action="{{ route('kurssit.courses.destroy', $course) }}" onsubmit="return confirm('Poistetaanko kurssi?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 underline">Poista</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä kursseja.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>