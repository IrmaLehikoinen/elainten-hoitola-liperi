@php
    $statusLabels = [
        'confirmed' => 'Vahvistettu',
        'pending' => 'Odottaa',
        'cancelled' => 'Peruttu',
        'completed' => 'Päättynyt',
    ];

    $isPast = ($mode ?? 'future') === 'past';
@endphp

    <div
    onclick="window.location.href='{{ route('admin.customers.show', $booking->customer_id) }}'"
    class="cursor-pointer rounded-lg border p-4 transition hover:shadow-md {{ $isPast ? 'opacity-75 hover:opacity-100' : '' }}"
    style="border-color: var(--brand-secondary);"
>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="font-semibold" style="color: var(--brand-text);">
                {{ $booking->customer->name ?? 'Tuntematon asiakas' }}
            </p>
            <p class="text-sm text-gray-500">
                {{ $booking->participants->pluck('name')->join(', ') }}
            </p>
        </div>

        <span
            class="shrink-0 rounded px-2 py-1 text-xs font-medium {{ $isPast ? '' : 'text-white' }}"
            style="{{ $isPast ? 'background-color: var(--brand-secondary); color: var(--brand-text);' : 'background-color: var(--brand-primary);' }}"
        >
            {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
        </span>
    </div>

        <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-600 sm:grid-cols-2">
        <p>
            <span class="font-medium" style="color: var(--brand-text);">{{ $isPast ? 'Saapui:' : 'Saapuu:' }}</span>
            {{ $booking->arrival_at?->format('d.m.Y H:i') ?? '—' }}
        </p>
        <p>
            <span class="font-medium" style="color: var(--brand-text);">{{ $isPast ? 'Noudettu:' : 'Noutaa:' }}</span>
            {{ $booking->pickup_at?->format('d.m.Y H:i') ?? '—' }}
        </p>
    </div>

    <div class="mt-2 grid grid-cols-1 gap-2 text-sm text-gray-600 sm:grid-cols-2">
        <p>
            <span class="font-medium" style="color: var(--brand-text);">Palvelu:</span>
            {{ (($careTypeLabels[$booking->care_type] ?? null)) ?: ($booking->care_type ? ucfirst(str_replace('_', ' ', $booking->care_type)) : '—') }}
        </p>
        <p>
            <span class="font-medium" style="color: var(--brand-text);">Summa:</span>
            {{ $booking->total_price !== null ? number_format((float) $booking->total_price, 2, ',', ' ') . ' €' : '—' }}
        </p>
    </div>

    <div class="mt-3 flex items-center gap-2 text-sm">
        <span class="font-medium" style="color: var(--brand-text);">Ennakkomaksu:</span>
        @if ((float) $booking->deposit_amount > 0)
            <span class="rounded px-2 py-0.5 text-xs font-medium text-white" style="background-color: {{ $booking->deposit_paid_at ? 'var(--brand-primary)' : '#b45309' }};">
                {{ number_format((float) $booking->deposit_amount, 2, ',', ' ') }} €
                {{ $booking->deposit_paid_at ? '· Maksettu' : '· Odottaa maksua' }}
            </span>
        @else
            <span class="text-gray-400">Ei käytössä</span>
        @endif
    </div>

    @if ($booking->notes)
        <p class="mt-3 rounded-md bg-gray-50 p-3 text-sm text-gray-600">
            {{ $booking->notes }}
        </p>
    @endif

    @if (($showCancel ?? false) && $booking->status !== 'cancelled')
        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="mt-3">
            @csrf
            <button
                type="submit"
                class="text-xs font-medium text-red-600"
                onclick="event.stopPropagation(); return confirm('Peruutetaanko tämä varaus?');"
            >
                Peruuta varaus
            </button>
        </form>
    @endif
</div>