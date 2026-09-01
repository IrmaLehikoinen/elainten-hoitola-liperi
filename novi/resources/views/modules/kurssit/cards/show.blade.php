<x-app-layout>
    <div class="p-6 max-w-3xl">
        <button type="button" onclick="window.location.href='{{ route('kurssit.cards.index') }}'"
            class="text-sm text-gray-500 hover:text-gray-800">← Kurssikortit</button>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-4 rounded-lg border bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-semibold">{{ $course->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        @if ($course->starts_at)
                            {{ $course->starts_at->format('d.m.Y H:i') }} ·
                        @endif
                        {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
                        · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
                        @if ($course->isFull())
                            · <span class="text-red-600 font-medium">Täynnä</span>
                        @endif
                    </p>
                </div>
                <button type="button" onclick="window.location.href='{{ route('kurssit.courses.edit', $course) }}'"
                    class="rounded-md border px-3 py-1.5 text-sm">Muokkaa kurssin tietoja</button>
            </div>

            <p class="mt-4 text-sm text-gray-700">{{ $course->short_description }}</p>
        </div>

        <h2 class="mt-8 text-sm font-semibold text-gray-500 uppercase tracking-wide">Osallistujat</h2>
        <div class="mt-3 space-y-2">
            @forelse ($course->registrations as $registration)
                <div class="rounded-lg border bg-white p-4 flex items-center justify-between">
                    <div class="cursor-pointer" onclick="window.location.href='{{ route('kurssit.registrations.show', $registration) }}'">
                        <p class="font-medium">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $registration->email }}
                            @if ($registration->phone) · {{ $registration->phone }} @endif
                            ·
                            @if ($registration->status === 'confirmed')
                                <span class="text-green-700 font-medium">Maksettu / vahvistettu</span>
                            @elseif ($registration->status === 'cancelled')
                                <span class="text-gray-400">Peruttu</span>
                            @else
                                <span class="text-amber-600 font-medium">Odottaa maksua</span>
                            @endif
                        </p>
                    </div>

                                        @if ($registration->status === 'confirmed')
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="window.open('{{ route('kurssit.invoices.print', $registration) }}', '_blank')"
                                class="text-sm text-gray-600 hover:text-gray-900">Kuitti</button>
                            <button type="button" onclick="window.open('{{ route('kurssit.invoices.print', ['registration' => $registration, 'type' => 'lasku']) }}', '_blank')"
                                class="text-sm text-gray-600 hover:text-gray-900">Lasku</button>
                        </div>
                    @elseif ($registration->status === 'pending')
                        <form method="POST" action="{{ route('kurssit.invoices.mark-paid', $registration) }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Merkitse maksetuksi paikan päällä</button>
                        </form>
                    @endif 
                </div>
            @empty
                <p class="text-sm text-gray-500">Ei vielä ilmoittautumisia.</p>
            @endforelse
        </div>

        <div class="mt-6 rounded-lg border bg-white p-5">
            <h2 class="text-sm font-semibold text-gray-700">Lisää osallistuja käsin</h2>
            <p class="mt-1 text-xs text-gray-500">Käytä tätä kun asiakas ilmoittautuu puhelimessa tai paikan päällä.</p>

            <form method="POST" action="{{ route('kurssit.cards.store-registration', $course) }}" class="mt-3 space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium">Nimi</label>
                        <input type="text" name="name" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Sähköposti</label>
                        <input type="email" name="email" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Puhelin</label>
                        <input type="text" name="phone" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    </div>
                                    </div>

                @if ($course->price > 0)
                    <div>
                        <label class="block text-sm font-medium mb-1">Maksutapa</label>
                        <div class="flex flex-col gap-2 text-sm">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="payment_choice" value="paid_now">
                                Maksettu heti (raha jo kädessä)
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="payment_choice" value="send_link" checked>
                                Lähetä maksulinkki sähköpostiin
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="payment_choice" value="pay_on_day">
                                Maksaa kurssipäivänä
                            </label>
                        </div>
                    </div>
                @endif
                <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-medium">Lisää osallistuja</button>
            </form>
        </div>
    </div>
</x-app-layout>