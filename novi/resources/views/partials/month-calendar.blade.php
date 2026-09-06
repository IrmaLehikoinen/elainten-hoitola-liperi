<div class="grid grid-cols-7 border-l border-t border-gray-200">
    @foreach (['Ma','Ti','Ke','To','Pe','La','Su'] as $label)
        <div class="border-b border-r border-gray-200 px-2 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $label }}</div>
    @endforeach

    @php $firstOffset = $days[0]['date']->dayOfWeekIso - 1; @endphp
    @for ($i = 0; $i < $firstOffset; $i++)
        <div class="aspect-square border-b border-r border-gray-200 bg-gray-50"></div>
    @endfor

    @foreach ($days as $day)
        @php $dayHref = route($dayRoute, ['date' => $day['date']->format('Y-m-d')]); @endphp
        <div
            onclick="window.location.href='{{ $dayHref }}'"
            class="aspect-square overflow-y-auto border-b border-r border-gray-200 p-2 hover:bg-gray-50 cursor-pointer"
            style="{{ $day['date']->isToday() ? 'background-color: var(--brand-secondary);' : '' }}"
        >
            <span class="text-xs font-semibold" style="color: var(--brand-text);">{{ $day['date']->format('j') }}</span>
            <div class="mt-1 space-y-1">
                @foreach ($day['entries'] as $entry)
                    @include('partials.calendar-pill', ['color' => $entry['color'], 'title' => $entry['title'], 'filled' => $entry['filled'] ?? true])
                @endforeach
            </div>
        </div>
    @endforeach
</div>