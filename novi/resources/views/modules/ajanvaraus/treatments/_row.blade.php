<div class="flex items-center justify-between rounded-md border border-gray-200 px-4 py-3">
    <div class="flex items-center gap-3">
        <span class="h-4 w-4 rounded-full border border-gray-300" style="background-color: {{ $treatment->color ?? '#ccc' }}"></span>
        <div>
            <p class="text-sm font-semibold">{{ $treatment->name }}</p>
            <p class="text-xs text-gray-500">
                {{ $treatment->duration_minutes }} min
                · {{ $treatment->capacity }} {{ $treatment->capacity === 1 ? 'henkilö' : 'henkilöä' }}
                · {{ $treatment->price > 0 ? number_format($treatment->price, 2, ',', ' ').' €' : 'Maksuton' }}
                @unless ($treatment->is_active)
                    · <span class="text-red-600">pois käytöstä</span>
                @endunless
            </p>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('ajanvaraus.public.book', $treatment) }}" target="_blank" class="text-sm font-semibold text-gray-500 hover:text-gray-800">Julkinen varaussivu ↗</a>
        <a href="{{ route('ajanvaraus.treatments.edit', $treatment) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Muokkaa →</a>
    </div>
</div>