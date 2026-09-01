<x-app-layout>
    <div class="p-6 max-w-3xl">
        <h1 class="text-xl font-semibold">Raportti</h1>
        <p class="mt-1 text-sm text-gray-500">Menneet kurssit ja niiden osallistujamäärät.</p>

        <form method="GET" action="{{ route('kurssit.reports.index') }}" class="mt-4 flex gap-2">
            <input type="text" name="q" value="{{ $search }}" placeholder="Hae kurssin nimellä"
                class="w-full rounded-md border-gray-300 text-sm">
            <button type="submit" class="btn-brand shrink-0 rounded-md px-4 py-2 text-sm font-medium">Hae</button>
        </form>

        <div class="mt-6 space-y-3">
            @forelse ($courses as $course)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
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
                <p class="text-sm text-gray-500">Ei vielä menneitä kursseja.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>