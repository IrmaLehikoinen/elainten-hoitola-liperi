<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-semibold">Kurssikortit</h1>
        <p class="mt-1 text-sm text-gray-500">Klikkaa kurssia nähdäksesi kaikki tiedot ja osallistujat.</p>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

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