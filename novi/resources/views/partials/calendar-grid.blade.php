@php
    $leadingBlanks = $periodStart->dayOfWeekIso - 1;
@endphp

<div class="grid grid-cols-7 border-l border-t border-gray-200">

    @foreach (['Ma', 'Ti', 'Ke', 'To', 'Pe', 'La', 'Su'] as $weekday)
        <div class="border-b border-r border-gray-200 px-2 py-2 text-center text-xs font-semibold text-gray-500">
            {{ $weekday }}
        </div>
    @endforeach

    @for ($i = 0; $i < $leadingBlanks; $i++)
        <div class="aspect-square border-b border-r border-gray-200 bg-gray-50"></div>
    @endfor

    @foreach ($days as $day)
        @php
            $dayHref = $day['count'] > 0
                ? route('admin.calendar.day', $day['date']->format('Y-m-d'))
                : null;
        @endphp

        <div
            @if ($dayHref) onclick="window.location.href='{{ $dayHref }}'" @endif
            class="aspect-square overflow-hidden border-b border-r border-gray-200 p-1.5 {{ $dayHref ? 'hover:bg-gray-50 cursor-pointer' : 'cursor-default' }}"
            style="{{ $day['date']->isToday() ? 'background-color: var(--brand-secondary);' : '' }}"
        >
            <div class="text-xs font-medium" style="color: var(--brand-text);">
                {{ $day['date']->day }}
            </div>

            @foreach ($day['participants']->groupBy(fn ($p) => mb_strtolower(trim($p->species))) as $species => $group)
                <div class="mt-1 truncate rounded px-1.5 py-0.5 text-[11px] font-medium text-white" style="background-color: var(--brand-primary);">
                    {{ ucfirst($species) }} {{ $group->count() }}
                </div>
            @endforeach
        </div>
    @endforeach

</div>