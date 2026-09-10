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
                    <h1 class="text-xl font-semibold">
                        {{ $course->name }}
                        @if ($course->isCancelled())
                            <span class="ml-2 rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 align-middle">Peruttu</span>
                        @elseif ($course->isRegistrationClosed())
                            <span class="ml-2 rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 align-middle">Ilmoittautuminen suljettu</span>
                        @endif
                    </h1>
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

            <div class="mt-4 flex flex-wrap gap-2 border-t pt-4">
                <form method="POST" action="{{ route('kurssit.courses.duplicate', $course) }}">
                    @csrf
                    <button type="submit" class="rounded-md border px-3 py-1.5 text-sm">Kopioi kurssiksi</button>
                </form>

                <form method="POST" action="{{ route('kurssit.courses.toggle-registration', $course) }}">
                    @csrf
                    <button type="submit" class="rounded-md border px-3 py-1.5 text-sm">
                        {{ $course->isRegistrationClosed() ? 'Avaa ilmoittautuminen' : 'Sulje ilmoittautuminen' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('kurssit.courses.toggle-cancelled', $course) }}"
                    onsubmit="return confirm('{{ $course->isCancelled() ? 'Poistetaanko peruutusmerkintä?' : 'Merkitäänkö kurssi peruutetuksi?' }}');">
                    @csrf
                    <button type="submit" class="rounded-md border px-3 py-1.5 text-sm text-red-600">
                        {{ $course->isCancelled() ? 'Poista peruutus' : 'Merkitse peruutetuksi' }}
                    </button>
                </form>
            </div>
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
                            · {{ $registration->created_at->format('d.m.Y H:i') }}
                            ·
                                            @if ($registration->status === 'confirmed')
                                <span class="text-green-700 font-medium">Maksettu / vahvistettu</span>
                            @elseif ($registration->status === 'cancelled')
                                <span class="text-gray-400">Peruttu</span>
                            @elseif ($registration->payment_choice === 'pay_on_day')
                                <span class="text-amber-600 font-medium">Odottaa maksua — maksaa kurssipäivänä</span>
                            @elseif ($registration->payment_choice === 'send_link')
                                <span class="text-amber-600 font-medium">Odottaa maksua — maksulinkki lähetetty</span>
                            @else
                                <span class="text-amber-600 font-medium">Odottaa maksua</span>
                            @endif
                        </p>
                    </div>

                                        @if ($registration->status === 'confirmed')
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="window.open('{{ route('kurssit.invoices.print', $registration) }}', '_blank')"
                                class="text-sm text-gray-600 hover:text-gray-900">Kuitti</button>
                        </div>
                    @elseif ($registration->status === 'pending')    
                        <form method="POST" action="{{ route('kurssit.invoices.mark-paid', $registration) }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Merkitse maksetuksi</button>
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

            @if ($course->isFull())
                <div class="mt-3 rounded-md bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">
                    Kurssi on jo täynnä ({{ $course->max_participants }} / {{ $course->max_participants }} paikkaa). Voit silti lisätä osallistujan tarvittaessa, mutta paikkoja ei enää virallisesti ole.
                </div>
            @endif

            <form method="POST" action="{{ route('kurssit.cards.store-registration', $course) }}" class="mt-3 space-y-3"
                @if ($course->isFull()) onsubmit="return confirm('Kurssi on täynnä. Lisätäänkö osallistuja silti?');" @endif>
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