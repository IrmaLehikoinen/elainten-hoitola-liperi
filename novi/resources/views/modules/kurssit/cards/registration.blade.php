<x-app-layout>
    <div class="p-6 max-w-xl">
        <button type="button" onclick="window.location.href='{{ route('kurssit.cards.show', $registration->course_id) }}'"
            class="text-sm text-gray-500 hover:text-gray-800">← {{ $registration->course->name }}</button>

        <div class="mt-4 rounded-lg border bg-white p-5">
            <h1 class="text-xl font-semibold">{{ $registration->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Ilmoittautunut {{ $registration->created_at->format('d.m.Y H:i') }}</p>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <dt class="text-gray-500">Sähköposti</dt>
                    <dd class="font-medium">{{ $registration->email }}</dd>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <dt class="text-gray-500">Puhelin</dt>
                    <dd class="font-medium">{{ $registration->phone ?: '—' }}</dd>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <dt class="text-gray-500">Kurssi</dt>
                    <dd class="font-medium">{{ $registration->course->name }}</dd>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <dt class="text-gray-500">Tila</dt>
                                    <dd class="font-medium">
                        @if ($registration->status === 'confirmed')
                            <span class="text-green-700">Maksettu / vahvistettu</span>
                        @elseif ($registration->status === 'cancelled')
                            <span class="text-gray-400">Peruttu</span>
                        @elseif ($registration->payment_choice === 'pay_on_day')
                            <span class="text-amber-600">Odottaa maksua — maksaa kurssipäivänä</span>
                        @elseif ($registration->payment_choice === 'send_link')
                            <span class="text-amber-600">Odottaa maksua — maksulinkki lähetetty</span>
                        @else
                            <span class="text-amber-600">Odottaa maksua</span>
                        @endif
                    </dd>
                </div>
                @if ($registration->paid_at)
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Maksettu</dt>
                        <dd class="font-medium">
                            {{ $registration->paid_at->format('d.m.Y H:i') }}
                            ({{ $registration->payment_method === 'manual' ? 'paikan päällä' : 'Stripe' }})
                        </dd>
                    </div>
                @endif
            </dl>

            <div class="mt-5 flex flex-wrap items-center gap-3">
                                @if ($registration->status === 'confirmed')
                    <button type="button" onclick="window.open('{{ route('kurssit.invoices.print', $registration) }}', '_blank')"
                        class="rounded-md border px-3 py-1.5 text-sm">Kuitti</button>
                    <button type="button" onclick="window.open('{{ route('kurssit.invoices.print', ['registration' => $registration, 'type' => 'lasku']) }}', '_blank')"
                        class="rounded-md border px-3 py-1.5 text-sm">Lasku</button>
                                    @elseif ($registration->status === 'pending')
                    <form method="POST" action="{{ route('kurssit.invoices.mark-paid', $registration) }}">
                        @csrf
                        <button type="submit" class="rounded-md border px-3 py-1.5 text-sm">Merkitse maksetuksi</button>
                    </form>
                @endif

                @if ($registration->status !== 'cancelled')
                    <form method="POST" action="{{ route('kurssit.registrations.cancel', $registration) }}"
                        onsubmit="return confirm('Perutaanko {{ $registration->name }} ilmoittautuminen?');">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Peru ilmoittautuminen</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>