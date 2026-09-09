<x-app-layout>
    <div class="p-6 max-w-2xl">
        <a href="{{ route('ajanvaraus.dashboard', ['date' => $date->format('Y-m-d')]) }}" class="text-sm text-gray-500 hover:text-gray-800">← Takaisin kalenteriin</a>

        <h1 class="mt-2 text-xl font-semibold">{{ $date->translatedFormat('l j.n.Y') }}</h1>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <div class="mt-6 space-y-2">
            @foreach ($blocks as $block)
                <div class="rounded-md border-l-4 px-3 py-2 text-sm bg-green-50" style="border-color: {{ $blockColor }}">
                    {{ $block->reason ?: 'Ei vapaita aikoja' }}
                    @if ($block->start_time)
                        klo {{ substr($block->start_time, 0, 5) }}–{{ substr($block->end_time, 0, 5) }}
                    @else
                        (koko päivä)
                    @endif
                </div>
            @endforeach

            @foreach ($external as $entry)
                <div class="rounded-md border-l-4 px-3 py-2 text-sm bg-gray-50" style="border-color: {{ $entry['color'] }}">
                    <span class="font-semibold">Kurssi:</span> {{ $entry['title'] }}
                </div>
            @endforeach

                        @foreach ($appointments as $appointment)
                <a href="{{ route('ajanvaraus.treatments.edit', $appointment->treatment_id) }}" class="block rounded-md border-l-4 px-3 py-2 text-sm bg-gray-50 hover:bg-gray-100" style="border-color: {{ $appointment->treatment->color ?? '#999' }}">
                    <span class="font-semibold">{{ $appointment->treatment->name }}</span> klo {{ $appointment->starts_at->format('H:i') }}–{{ $appointment->ends_at->format('H:i') }}
                    · {{ $appointment->name }} ({{ $appointment->email }})
                </a>
            @endforeach

            @foreach ($recurring as $r)
                <a href="{{ route('ajanvaraus.treatments.edit', $r['treatment']->id) }}" class="block rounded-md border-l-4 px-3 py-2 text-sm bg-gray-50 hover:bg-gray-100" style="border-color: {{ $r['treatment']->color ?? '#999' }}">
                    <span class="font-semibold">{{ $r['title'] }}</span> — ei vielä varauksia, avaa muokataksesi
                </a>
            @endforeach

            @if ($appointments->isEmpty() && empty($external) && $recurring->isEmpty() && $blocks->isEmpty())
                <p class="text-sm text-gray-400">Ei varauksia tälle päivälle.</p>
            @endif
        </div>

        <div class="mt-10 border-t pt-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Avaa tämä päivä hoidolle</h2>
            <p class="mt-1 text-xs text-gray-400">Lisää ylimääräinen avaus jollekin hoidolle juuri tälle päivälle, viikkoaikataulun lisäksi.</p>

            <form method="POST" action="{{ route('ajanvaraus.openings.store-any') }}" class="mt-4 flex items-end gap-2 flex-wrap">
                @csrf
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Hoito</label>
                    <select name="treatment_id" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                        @foreach ($treatments as $treatment)
                            <option value="{{ $treatment->id }}">{{ $treatment->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Alkaa</label>
                    <input type="time" name="start_time" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Päättyy</label>
                    <input type="time" name="end_time" required class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Avaa tämä päivä</button>
            </form>
        </div>
    </div>
</x-app-layout>