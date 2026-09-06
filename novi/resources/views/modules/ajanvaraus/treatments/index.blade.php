<x-app-layout>
    <div class="p-6 max-w-3xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Hoidot</h1>
            <a href="{{ route('ajanvaraus.treatments.create') }}" class="btn-brand rounded-md px-4 py-2 text-sm font-semibold">Lisää hoito</a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <div class="mt-6 rounded-md border border-gray-200 p-4">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-400">Otsikot</h2>
            <p class="mt-1 text-xs text-gray-400">Ryhmittele hoidot otsikoiden alle, esim. "Terapia" tai "Rauhoittava jooga".</p>

            <div class="mt-3 space-y-2">
                @forelse ($categories as $category)
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('ajanvaraus.categories.update', $category) }}" class="flex-1 flex items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="text" name="name" value="{{ $category->name }}" class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                            <button type="submit" class="text-xs text-gray-600 hover:text-gray-900">Tallenna</button>
                        </form>
                        <form method="POST" action="{{ route('ajanvaraus.categories.destroy', $category) }}" onsubmit="return confirm('Poistetaanko otsikko?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">Poista</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Ei vielä otsikoita.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('ajanvaraus.categories.store') }}" class="mt-4 flex items-end gap-2">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Uusi otsikko</label>
                    <input type="text" name="name" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Lisää</button>
            </form>
        </div>

        @php
            $grouped = $treatments->groupBy('treatment_category_id');
        @endphp

        <div class="mt-6 space-y-6">
            @foreach ($categories as $category)
                @if ($grouped->has($category->id))
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $category->name }}</h2>
                        <div class="mt-2 space-y-3">
                            @foreach ($grouped[$category->id] as $treatment)
                                @include('ajanvaraus::treatments._row', ['treatment' => $treatment])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($grouped->has(''))
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Ei otsikkoa</h2>
                    <div class="mt-2 space-y-3">
                        @foreach ($grouped[''] as $treatment)
                            @include('ajanvaraus::treatments._row', ['treatment' => $treatment])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($treatments->isEmpty())
                <p class="text-sm text-gray-500">Ei vielä hoitoja. Lisää ensimmäinen hoito yllä olevasta napista.</p>
            @endif
        </div>
    </div>
</x-app-layout>