<x-layouts.public :company="$company">
    <h2 class="text-lg font-semibold" style="color: var(--brand-text);">
        2. Valitse vapaa aloituspäivä
    </h2>

    <div class="mt-6 space-y-2">
        @forelse ($dates as $date)
            <form method="POST" action="{{ route('public.booking.hold') }}">
                @csrf
                <input type="hidden" name="start_date" value="{{ $date['iso'] }}">
                <button type="submit" class="w-full rounded-md border px-4 py-3 text-left text-sm font-medium hover:shadow-md" style="border-color: var(--brand-secondary); color: var(--brand-text);">
                    {{ $date['display'] }}
                </button>
            </form>
        @empty
            <p class="text-sm text-gray-500">
                Valitettavasti tälle kokoonpanolle ei löytynyt vapaita aikoja lähitulevaisuudesta. Ota yhteyttä suoraan hoitolaan.
            </p>
        @endforelse
    </div>

    <div class="mt-6">
        <a href="{{ route('public.booking.start') }}" class="text-sm font-medium" style="color: var(--brand-primary);">
            ← Muuta hakuehtoja
        </a>
    </div>
</x-layouts.public>