<x-app-layout>
    <x-slot name="header">
        <div>
            <h1
                class="text-2xl font-semibold"
                style="color: var(--brand-text); font-family: var(--brand-heading-font);"
            >
                {{ ucfirst($day->translatedFormat('l j.n.Y')) }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kaikki tämän päivän varaukset
            </p>
        </div>
    </x-slot>

    <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div onclick="window.location.href='{{ route('calendar.index') }}'" class="cursor-pointer text-sm font-medium" style="color: var(--brand-primary);">
                ← Takaisin kalenteriin
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            <div>
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                        style="background-color: var(--brand-secondary);"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                    </span>
                   <h2 class="text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-primary);">
                        Paikalla tänään
                    </h2>
                </div>

                <div class="mt-3 space-y-3">
                    @forelse ($bookings as $entry)
                        @php $customer = $entry['customer']; @endphp

                     <div class="bg-white p-5 shadow-sm rounded-lg">
                            <div class="divide-y">
                                @foreach ($entry['participants'] as $p)
                                    <div class="flex items-center justify-between gap-4 py-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold" style="color: var(--brand-text);">
                                                {{ $p->name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $customer->name ?? 'Tuntematon asiakas' }}
                                            </p>
                                        </div>

                                        @if ($p->pet_id && Route::has('admin.pets.show'))
                                            <div
                                                onclick="window.location.href='{{ route('admin.pets.show', $p->pet_id) }}'"
                                                class="shrink-0 cursor-pointer text-sm font-medium"
                                                style="color: var(--brand-primary);"
                                            >
                                                Avaa →
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>  
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">
                            Ei varauksia tälle päivälle.
                        </p>
                    @endforelse
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-primary);">
                    Muistutukset tälle päivälle
                </h2>

                <div class="mt-3 divide-y rounded-lg bg-white shadow-sm">
                    @forelse ($reminders as $reminder)
                        @php
                            $reminderPetId = $reminder->pet_id ?? optional($reminder->bookingParticipant)->pet_id;
                            $reminderName = optional($reminder->pet)->name ?? optional($reminder->bookingParticipant)->name;
                            $reminderCustomer = optional($reminder->customer)->name
                                ?? optional(optional(optional($reminder->bookingParticipant)->booking)->customer)->name;
                        @endphp
                        <div class="flex items-center gap-4 p-4">
                            <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-primary);">
                                {{ $reminder->due_at->format('H:i') }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium" style="color: var(--brand-text);">
                                    {{ $reminder->title ?: \App\Http\Controllers\DashboardController::typeLabel($reminder->type) }}{{ $reminderName ? ' – '.$reminderName : '' }}
                                </p>
                                @if ($reminderCustomer)
                                    <p class="truncate text-xs text-gray-500">{{ $reminderCustomer }}</p>
                                @endif
                            </div>

                            @if ($reminderPetId && Route::has('admin.pets.show'))
                                <div
                                    onclick="window.location.href='{{ route('admin.pets.show', $reminderPetId) }}'"
                                    class="shrink-0 cursor-pointer text-sm font-medium"
                                    style="color: var(--brand-primary);"
                                >
                                    Avaa →
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="p-4 text-center text-sm text-gray-500">
                            Ei muistutuksia tälle päivälle.
                        </p>
                    @endforelse
                </div>
            </div>

            </div>

            <div>
              <h2 class="flex items-center gap-2 text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-primary);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v14m0 0l-5-5m5 5l5-5" />
                    </svg>
                    Saapuvat tänään
                </h2>  

                <div class="mt-3 divide-y rounded-lg bg-white shadow-sm">
                    @forelse ($arrivingToday as $p)
                        <div class="flex items-center gap-4 p-4">
                            <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-primary);">
                                {{ optional($p->booking)->arrival_at?->format('H:i') ?? '--:--' }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold" style="color: var(--brand-text);">
                                    {{ $p->name }}
                                </p>
                                <p class="truncate text-xs text-gray-500">
                                    {{ optional(optional($p->booking)->customer)->name }}
                                </p>
                            </div>

                            @if ($p->pet_id && Route::has('admin.pets.show'))
                                <div
                                    onclick="window.location.href='{{ route('admin.pets.show', $p->pet_id) }}'"
                                    class="shrink-0 cursor-pointer text-sm font-medium"
                                    style="color: var(--brand-primary);"
                                >
                                    Avaa →
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="p-4 text-center text-sm text-gray-500">
                            Ei saapumisia tälle päivälle.
                        </p>
                    @endforelse
                </div>
            </div>

            <div>
             <h2 class="flex items-center gap-2 text-lg font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-primary);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--brand-secondary)" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20V6m0 0l-5 5m5-5l5 5" />
                    </svg>
                    Lähtevät tänään
                </h2>  

                <div class="mt-3 divide-y rounded-lg bg-white shadow-sm">
                    @forelse ($leavingToday as $p)
                        <div class="flex items-center gap-4 p-4">
                            <span class="w-14 shrink-0 text-sm font-semibold" style="color: var(--brand-secondary);">
                                {{ optional($p->booking)->pickup_at?->format('H:i') ?? '--:--' }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold" style="color: var(--brand-text);">
                                    {{ $p->name }}
                                </p>
                                <p class="truncate text-xs text-gray-500">
                                    {{ optional(optional($p->booking)->customer)->name }}
                                </p>
                            </div>

                            @if ($p->pet_id && Route::has('admin.pets.show'))
                                <div
                                    onclick="window.location.href='{{ route('admin.pets.show', $p->pet_id) }}'"
                                    class="shrink-0 cursor-pointer text-sm font-medium"
                                    style="color: var(--brand-primary);"
                                >
                                    Avaa →
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="p-4 text-center text-sm text-gray-500">
                            Ei lähtöjä tälle päivälle.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>