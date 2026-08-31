<x-kurssit::layouts.public :title="$course->name">
    @if (session('registration_error'))
        <div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B; padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:20px;">
            {{ session('registration_error') }}
        </div>
    @endif

    <h1 style="font-family: var(--brand-heading-font, serif); font-size: 24px; color: var(--brand-text, #2A3428); margin-bottom:16px;">
        {{ $course->name }}
    </h1>

    @if ($course->starts_at)
        <p style="color:#6b7280; font-size:14px; margin-bottom:4px;">{{ $course->starts_at->format('d.m.Y H:i') }}</p>
    @endif

    <p style="color:#6b7280; font-size:14px; margin-bottom:20px;">
        {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
        · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
    </p>

    @if ($course->presentation_type === 'brochure' && $course->brochure_path)
        <p style="margin-bottom:20px;">
            <button type="button" onclick="window.location.href='{{ \Illuminate\Support\Facades\Storage::url($course->brochure_path) }}'"
                style="background:#F3F4F6; color:#2A3428; border:none; padding:10px 18px; border-radius:8px; font-size:14px; cursor:pointer;">
                Avaa esite
            </button>
        </p>
    @elseif ($course->description_html)
        <div style="color:#374151; font-size:15px; line-height:1.6; margin-bottom:24px;">
            {!! $course->description_html !!}
        </div>
    @endif

    @if ($course->isFull())
        <p style="color:#991B1B; font-weight:600;">Kurssi on valitettavasti täynnä.</p>
    @else
        <form method="POST" action="{{ route('kurssit.public.store', $course) }}" style="margin-top:24px; display:flex; flex-direction:column; gap:14px; max-width:400px;">
            @csrf

            <div>
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:4px;">Nimi</label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius:8px; box-sizing:border-box;">
                @error('name') <p style="color:#991B1B; font-size:13px; margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:4px;">Sähköposti</label>
                <input type="email" name="email" value="{{ old('email') }}" required style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius:8px; box-sizing:border-box;">
                @error('email') <p style="color:#991B1B; font-size:13px; margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:4px;">Puhelin (valinnainen)</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius:8px; box-sizing:border-box;">
            </div>

            <button type="submit"
                style="margin-top:8px; background: var(--brand-primary, #3F4F3A); color:white; border:none; padding:12px 20px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer;">
                {{ $course->price > 0 ? 'Jatka maksuun' : 'Ilmoittaudu' }}
            </button>
        </form>
    @endif
</x-kurssit::layouts.public>