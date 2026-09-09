@php
    $leadingBlanks = $periodStart->dayOfWeekIso - 1;
    $availabilityService = app(\App\Services\AvailabilityService::class);
@endphp

@if (($view ?? 'month') === 'day')
    @php $day = $days[0] ?? null; @endphp

    @if ($day)
        @php
            $dayHref = route('admin.calendar.day', $day['date']->format('Y-m-d'));
            $usage = $availabilityService->usageForDate($day['date']);
            $isBlocked = \App\Models\DateCapacityOverride::whereDate('date', $day['date']->toDateString())
                ->whereNull('resource_type')
                ->where('capacity', 0)
                ->exists();
        @endphp

        <div
            onclick="window.location.href='{{ $dayHref }}'"
            class="mx-auto max-w-md min-h-[300px] rounded-lg border border-gray-200 p-6 hover:bg-gray-50 cursor-pointer"
            style="{{ $day['date']->isToday() ? 'background-color: var(--brand-secondary);' : '' }}"
        >
            <div class="flex items-center justify-between gap-2">
                <span class="text-lg font-semibold" style="color: var(--brand-text);">
                    {{ ucfirst($day['date']->translatedFormat('l j.n.Y')) }}
                </span>

                @if ($day['has_new'] ?? false)
                    <span
                        onclick="event.stopPropagation(); window.location.href='{{ !empty($day['new_booking_id']) ? route('admin.bookings.acknowledge', $day['new_booking_id']) : $dayHref }}'"
                        class="rounded px-2 py-1 text-xs font-semibold text-white cursor-pointer"
                        style="background-color: #D98C7A;"
                    >
                        Uusi
                    </span>
                @elseif ($isBlocked)
                    <span class="rounded px-2 py-1 text-xs font-semibold text-white" style="background-color: var(--brand-secondary);">
                        Suljettu
                    </span>
                @endif
            </div>

            <div class="mt-4 space-y-2">
                @foreach ($usage as $species => $info)
                    @if ($info['used'] > 0 || $info['overridden'])
                        @php $isFull = $info['used'] >= $info['capacity']; @endphp
                        <div
                            class="rounded px-2 py-1.5 text-sm font-medium text-white text-center"
                            style="background-color: var(--brand-primary);"
                        >
                            {{ ucfirst($species) }} {{ $info['used'] }}/{{ $info['capacity'] }}{{ $isFull ? ' · Täynnä' : '' }}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
@else
<div class="grid grid-cols-7 border-l border-t border-gray-200">

    @foreach (['Ma', 'Ti', 'Ke', 'To', 'Pe', 'La', 'Su'] as $weekday)
        <div class="border-b border-r border-gray-200 px-2 py-2 text-center text-xs font-semibold text-gray-500">
            {{ $weekday }}
        </div>
    @endforeach

         @for ($i = 0; $i < $leadingBlanks; $i++)
                <div class="aspect-square max-[639px]:aspect-auto max-[639px]:min-h-[76px] border-b border-r border-gray-200 bg-gray-50"></div>
    @endfor   

    @foreach ($days as $day)
        @php
            $dayHref = route('admin.calendar.day', $day['date']->format('Y-m-d'));
            $usage = $availabilityService->usageForDate($day['date']);
            $isBlocked = \App\Models\DateCapacityOverride::whereDate('date', $day['date']->toDateString())
                ->whereNull('resource_type')
                ->where('capacity', 0)
                ->exists();
        @endphp

        <div
                        onclick="window.location.href='{{ $dayHref }}'"
                        class="aspect-square max-[639px]:aspect-auto max-[639px]:min-h-[76px] overflow-hidden border-b border-r border-gray-200 p-1.5 hover:bg-gray-50 cursor-pointer"
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
                        class="mt-1 rounded px-1 py-0.5 text-[9px] sm:text-[11px] leading-tight font-medium text-white text-center"
                        style="background-color: var(--brand-primary);"
                    >
                        {{ ucfirst($species) }} {{ $info['used'] }}/{{ $info['capacity'] }}{{ $isFull ? ' · Täynnä' : '' }}
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach

</div>
@endif