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
        @php $hasCourses = $day['courses']->isNotEmpty(); @endphp
                <div
            onclick="pickDay('{{ $day['date']->format('Y-m-d') }}', {{ $hasCourses ? $day['courses']->first()->id : 'null' }})"
            class="aspect-square overflow-hidden border-b border-r border-gray-200 p-1.5 cursor-pointer"
            style="{{ $day['date']->isToday() ? 'background-color: var(--brand-secondary);' : '' }}"
        >
            <span class="text-xs font-medium text-gray-700">{{ $day['date']->day }}</span>

            @foreach ($day['courses'] as $course)
                <div class="mt-1 truncate rounded px-1.5 py-0.5 text-[10px] font-medium"
                    style="background-color: var(--brand-primary); color: #FFFFFF;">
                    {{ \Illuminate\Support\Str::limit($course->name, 14) }}
                </div>
            @endforeach
        </div>
    @endforeach

</div>