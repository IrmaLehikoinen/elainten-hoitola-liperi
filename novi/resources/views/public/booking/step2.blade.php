<x-layouts.public :step="2" :total-steps="5">
    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
        Valitse vapaa aloituspäivä
    </h2>

    <div class="mt-6 space-y-2">
        @forelse ($dates as $date)
            <form method="POST" action="{{ route('public.booking.hold') }}">
                @csrf
                <input type="hidden" name="start_date" value="{{ $date['iso'] }}">
                <button type="submit" class="w-full rounded-lg border-2 px-4 py-3 text-left text-sm font-medium hover:shadow-md" style="border-color: var(--brand-secondary); color: var(--brand-text);">
                    {{ $date['display'] }}
                </button>
            </form>
        @empty
            <p class="text-sm" style="color: var(--brand-text); opacity: 0.7;">
                Valitettavasti tälle kokoonpanolle ei löytynyt vapaita aikoja lähitulevaisuudesta. Ota yhteyttä suoraan hoitolaan.
            </p>
        @endforelse
    </div>

    <div class="mt-6">
        <span onclick="window.location.href='{{ route('public.booking.start') }}'" class="cursor-pointer text-sm font-medium" style="color: var(--brand-primary);">
            ← Muuta hakuehtoja
        </span>
    </div>
</x-layouts.public>