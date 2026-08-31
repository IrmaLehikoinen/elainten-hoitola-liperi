<x-kurssit::layouts.public title="Kurssit">
    <h1 style="font-family: var(--brand-heading-font, serif); font-size: 26px; color: var(--brand-text, #2A3428); margin-bottom: 24px;">
        Kurssit
    </h1>

    @if ($courses->isEmpty())
        <p style="color:#5A5A5A;">Ei tällä hetkellä avoimia kursseja.</p>
    @endif

    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach ($courses as $course)
            <div style="background:white; border-radius:12px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                <h2 style="font-size:18px; font-weight:600; color: var(--brand-text, #2A3428); margin:0 0 6px;">
                    {{ $course->name }}
                </h2>

                @if ($course->starts_at)
                    <p style="color:#6b7280; font-size:14px; margin:0 0 4px;">{{ $course->starts_at->format('d.m.Y H:i') }}</p>
                @endif

                <p style="color:#6b7280; font-size:14px; margin:0 0 12px;">
                    {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
                    · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
                </p>

                @if ($course->isFull())
                    <span style="display:inline-block; padding:6px 14px; border-radius:6px; background:#F3F4F6; color:#6b7280; font-size:14px;">Täynnä</span>
                @else
                    <button type="button" onclick="window.location.href='{{ route('kurssit.public.register', $course) }}'"
                        style="background: var(--brand-primary, #3F4F3A); color:white; border:none; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                        Ilmoittaudu
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</x-kurssit::layouts.public>