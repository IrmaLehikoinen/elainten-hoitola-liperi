<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                    Kalenteri —
                    @if ($calendarView === 'day')
                        {{ ucfirst($calendarAnchor->translatedFormat('l j.n.Y')) }}
                    @elseif ($calendarView === 'week')
                        {{ $calendarMonth->translatedFormat('j.n.') }} – {{ $calendarMonth->copy()->endOfWeek(\Illuminate\Support\Carbon::SUNDAY)->translatedFormat('j.n.Y') }}
                    @else
                        {{ $calendarMonth->translatedFormat('F Y') }}
                    @endif
                </h1>

                <div class="mt-1 h-5"></div>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $prevAnchor = match ($calendarView) {
                        'day' => $calendarAnchor->copy()->subDay(),
                        'week' => $calendarAnchor->copy()->subWeek(),
                        default => $calendarAnchor->copy()->subMonth(),
                    };
                    $nextAnchor = match ($calendarView) {
                        'day' => $calendarAnchor->copy()->addDay(),
                        'week' => $calendarAnchor->copy()->addWeek(),
                        default => $calendarAnchor->copy()->addMonth(),
                    };
                @endphp
                <a href="{{ route('ajanvaraus.dashboard', ['view' => $calendarView, 'date' => $prevAnchor->format('Y-m-d')]) }}" class="rounded-md border px-3 py-1 text-sm" style="border-color: var(--brand-secondary); color: var(--brand-text);">← Edellinen</a>
                <a href="{{ route('ajanvaraus.dashboard', ['view' => $calendarView, 'date' => $nextAnchor->format('Y-m-d')]) }}" class="rounded-md border px-3 py-1 text-sm" style="border-color: var(--brand-secondary); color: var(--brand-text);">Seuraava →</a>

                <div class="ml-2 flex gap-1">
                    <a href="{{ route('ajanvaraus.dashboard', ['view' => 'month', 'date' => $calendarAnchor->format('Y-m-d')]) }}" class="rounded-md px-3 py-1 text-sm" style="{{ $calendarView === 'month' ? 'background-color: var(--brand-secondary);' : 'border: 1px solid var(--brand-secondary);' }} color: var(--brand-text);">Kuukausi</a>
                    <a href="{{ route('ajanvaraus.dashboard', ['view' => 'week', 'date' => $calendarAnchor->format('Y-m-d')]) }}" class="rounded-md px-3 py-1 text-sm" style="{{ $calendarView === 'week' ? 'background-color: var(--brand-secondary);' : 'border: 1px solid var(--brand-secondary);' }} color: var(--brand-text);">Viikko</a>
                    <a href="{{ route('ajanvaraus.dashboard', ['view' => 'day', 'date' => $calendarAnchor->format('Y-m-d')]) }}" class="rounded-md px-3 py-1 text-sm" style="{{ $calendarView === 'day' ? 'background-color: var(--brand-secondary);' : 'border: 1px solid var(--brand-secondary);' }} color: var(--brand-text);">Päivä</a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.month-calendar', ['days' => $calendarDays, 'dayRoute' => 'ajanvaraus.calendar.day', 'view' => $calendarView])
    </div>
</x-app-layout>