<x-kurssit::layouts.public :title="$course->name">
    @include('kurssit::partials.brand-styles')

    <style>
        .sp-kurssit-page {
            --brand-primary: #7CAB33;
            --sp-rose: #80107A;
            --sp-rose-soft: #C4DD5E;
            --sp-gold: #B49170;
            --sp-gold-light: #C2AF6F;
        }
    </style>

    @include('kurssit::partials.nav')

    <div class="sp-kurssit-page">

    @if (session('registration_error'))
        <div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B; padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:20px;">
            {{ session('registration_error') }}
        </div>
    @endif

    @php
        $blocks = $course->presentation_type === 'blocks' ? ($course->content_blocks ?? []) : [];
        $heroBlock = null;
        $remainingBlocks = $blocks;

        if (! empty($blocks) && ($blocks[0]['type'] ?? null) === 'image_full') {
            $heroBlock = $blocks[0];
            $remainingBlocks = array_slice($blocks, 1);
        }

        $brochureExt = $course->brochure_path ? strtolower(pathinfo($course->brochure_path, PATHINFO_EXTENSION)) : null;
        $brochureIsImage = in_array($brochureExt, ['jpg', 'jpeg', 'png']);
    @endphp

    @if ($course->presentation_type === 'brochure' && $course->brochure_path)
        @if ($brochureIsImage)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($course->brochure_path) }}"
                style="width:100%; border-radius: var(--brand-radius, 16px); margin-bottom:24px; display:block;">
        @else
            <iframe src="{{ \Illuminate\Support\Facades\Storage::url($course->brochure_path) }}"
                style="width:100%; height:80vh; border:none; border-radius: var(--brand-radius, 16px); margin-bottom:24px; display:block;"></iframe>
        @endif
    @endif

    @if ($heroBlock)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($heroBlock['path']) }}"
            style="width:100%; border-radius: var(--brand-radius, 16px); margin-bottom:24px; display:block;">
    @endif

    <div style="background:white; border-radius: var(--brand-radius, 16px); padding:24px 28px; box-shadow:0 1px 4px rgba(42,52,40,0.05); border:1px solid rgba(42,52,40,0.08); margin-bottom:32px;">
        <h1 style="font-family: var(--brand-heading-font); font-size: 24px; font-weight:600; color: var(--brand-text); margin:0 0 10px;">
            {{ $course->name }}
        </h1>

        @if ($course->starts_at)
            <p style="font-family: var(--brand-heading-font); font-size:15px; font-weight:600; color: var(--brand-text); margin:0 0 4px;">
                {{ $course->starts_at->format('d.m.Y H:i') }}
            </p>
        @endif

        <p style="font-family: var(--brand-body-font); font-size:15px; color: var(--brand-text); opacity:0.75; margin:0;">
            {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
            · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
        </p>
    </div>

    @if (! empty($remainingBlocks))
        <div style="margin-bottom:32px;">
            @foreach ($remainingBlocks as $block)
                @if ($block['type'] === 'heading')
                    <h2 style="font-family: var(--brand-heading-font); font-size:22px; font-weight:600; color: var(--brand-text); margin:24px 0 12px;">
                        {{ $block['text'] }}
                    </h2>
                @elseif ($block['type'] === 'subheading')
                    <h3 style="font-family: var(--brand-heading-font); font-size:18px; font-weight:600; color: var(--brand-text); margin:20px 0 8px;">
                        {{ $block['text'] }}
                    </h3>
                @elseif ($block['type'] === 'paragraph')
                    <p style="font-family: var(--brand-body-font); color: var(--brand-text); font-size:15px; line-height:1.7; margin-bottom:14px;">{{ $block['text'] }}</p>
                @elseif ($block['type'] === 'image_full')
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($block['path']) }}" style="width:100%; border-radius: var(--brand-radius, 10px); margin:16px 0;">
                @elseif ($block['type'] === 'image_side')
                    <div style="display:flex; gap:20px; align-items:flex-start; margin:16px 0; {{ ($block['align'] ?? 'left') === 'right' ? 'flex-direction:row-reverse;' : '' }}">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($block['path']) }}" style="width:40%; border-radius: var(--brand-radius, 10px);">
                        <p style="flex:1; font-family: var(--brand-body-font); color: var(--brand-text); font-size:15px; line-height:1.7;">{{ $block['text'] ?? '' }}</p>
                    </div>
                @elseif ($block['type'] === 'checklist')
                    <ul style="list-style:none; padding:0; margin:16px 0;">
                        @foreach ($block['items'] as $item)
                            <li style="display:flex; gap:8px; align-items:flex-start; margin-bottom:8px; font-family: var(--brand-body-font); font-size:15px; color: var(--brand-text);">
                                <span style="color: var(--sp-rose); font-weight:700;">✓</span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </div>
    @endif

    @if ($course->isCancelled())
        <p style="color:#991B1B; font-weight:600;">Tämä kurssi on peruttu.</p>
    @elseif ($course->isRegistrationClosed())
        <p style="color: var(--brand-text); font-weight:600;">Ilmoittautuminen tälle kurssille on suljettu.</p>
    @elseif ($course->isFull())
        @if ($course->isTemporarilyFull())
            <p style="color: var(--brand-text); font-weight:600;">
                Kurssi on juuri nyt varattu täyteen, mutta varauksia ei vielä ole viety loppuun asti. Paikkoja voi vapautua muutaman minuutin kuluessa — käy katsomassa tilanne hetken päästä uudelleen.
            </p>
        @else
            <p style="color:#991B1B; font-weight:600;">Kurssi on valitettavasti täynnä.</p>
        @endif
    @else
        <div style="background:white; border-radius: var(--brand-radius, 16px); padding:28px; box-shadow:0 1px 4px rgba(42,52,40,0.05); border:1px solid rgba(42,52,40,0.08);">
            <h2 style="font-family: var(--brand-heading-font); font-size:18px; font-weight:600; color: var(--brand-text); margin:0 0 16px;">
                Ilmoittaudu
            </h2>

            <form method="POST" action="{{ route('kurssit.public.store', $course) }}" style="display:flex; flex-direction:column; gap:14px; max-width:400px; margin:0 auto;">
                @csrf

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Nimi</label>
                    <input type="text" name="name" value="{{ old('name') }}" required style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                    @error('name') <p style="color:#991B1B; font-size:13px; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Sähköposti</label>
                    <input type="email" name="email" value="{{ old('email') }}" required style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                    @error('email') <p style="color:#991B1B; font-size:13px; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Puhelin (valinnainen)</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                </div>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Lahjakortin numero (valinnainen)</label>
                    <input type="text" name="gift_card_code" value="{{ old('gift_card_code') }}" style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                </div>

                <label style="display:flex; align-items:flex-start; gap:8px; font-size:13px; color: var(--brand-text); margin-top:4px;">
                    <input type="checkbox" name="privacy_consent" value="1" required style="margin-top:3px;">
                    <span>Hyväksyn <a href="{{ route('legal.privacy', $course->company) }}" target="_blank" style="color: var(--brand-primary); text-decoration:underline;">tietosuojaselosteen</a> ja annan luvan tietojeni käsittelyyn ilmoittautumisen toteuttamiseksi.</span>
                </label>
                @error('privacy_consent') <p style="color:#991B1B; font-size:13px; margin-top:-8px;">{{ $message }}</p> @enderror

                <button type="submit" class="sp-btn-cta" style="margin-top:8px; width:100%; justify-content:center;">
                    {{ $course->price > 0 ? 'Jatka maksuun' : 'Ilmoittaudu' }}
                </button>
            </form>
        </div>
    @endif

    @include('kurssit::partials.powered-by-novi')
    </div>
</x-kurssit::layouts.public>