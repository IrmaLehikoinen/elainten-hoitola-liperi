@php
    $leadingBlanks = $periodStart->dayOfWeekIso - 1;
    $availabilityService = app(\App\Services\AvailabilityService::class);
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
            $dayHref = route('admin.calendar.day', $day['date']->format('Y-m-d'));
            $usage = $availabilityService->usageForDate($day['date']);
            $isBlocked = \App\Modules\Lemmikkihoitola\Models\DateCapacityOverride::whereDate('date', $day['date']->toDateString())           
                ->whereNull('resource_type')
                ->where('capacity', 0)
                ->exists();
        @endphp

                <div
            onclick="window.location.href='{{ $dayHref }}'"
            class="aspect-square overflow-hidden border-b border-r border-gray-200 p-1.5 hover:bg-gray-50 cursor-pointer"
            style="{{ $day['date']->isToday() ? 'background-color: var(--brand-secondary);' : '' }}"
        >    
            <div class="flex items-center justify-between gap-1">
                <span class="text-xs font-medium" style="color: var(--brand-text);">
                    {{ $day['date']->day }}
                </span>

                                @if ($day['has_new'] ?? false)
                    <span
                        onclick="event.stopPropagation(); window.location.href='{{ !empty($day['new_booking_id']) ? route('admin.bookings.acknowledge', $day['new_booking_id']) : $dayHref }}'"
                        class="rounded px-1 text-[9px] font-semibold text-white cursor-pointer"
                        style="background-color: #D98C7A;"
                    >
                        Uusi
                    </span>
                @elseif ($isBlocked)   
                    <span class="rounded px-1 text-[9px] font-semibold text-white" style="background-color: var(--brand-secondary);">
                        Suljettu
                    </span>
                @endif
            </div>

            @foreach ($usage as $species => $info)
                @if ($info['used'] > 0 || $info['overridden'])
                    @php $isFull = $info['used'] >= $info['capacity']; @endphp
                                     <div
                        class="mt-1 truncate rounded px-1.5 py-0.5 text-[11px] font-medium text-white"
                        style="background-color: var(--brand-primary);"
                    >
                        {{ ucfirst($species) }} {{ $info['used'] }}/{{ $info['capacity'] }}{{ $isFull ? ' · Täynnä' : '' }}
                    </div>   
                @endif
            @endforeach
        </div>
    @endforeach

</div>