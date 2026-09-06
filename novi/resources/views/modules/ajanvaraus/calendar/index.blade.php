<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold" style="color: var(--brand-text); font-family: var(--brand-heading-font);">
                Kalenteri — {{ $calendarMonth->translatedFormat('F Y') }}
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('ajanvaraus.dashboard', ['date' => $calendarMonth->copy()->subMonth()->format('Y-m-d')]) }}" class="rounded-md border px-3 py-1 text-sm" style="border-color: var(--brand-secondary); color: var(--brand-text);">← Edellinen</a>
                <a href="{{ route('ajanvaraus.dashboard', ['date' => $calendarMonth->copy()->addMonth()->format('Y-m-d')]) }}" class="rounded-md border px-3 py-1 text-sm" style="border-color: var(--brand-secondary); color: var(--brand-text);">Seuraava →</a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.month-calendar', ['days' => $calendarDays, 'dayRoute' => 'ajanvaraus.calendar.day'])
    </div>
</x-app-layout>