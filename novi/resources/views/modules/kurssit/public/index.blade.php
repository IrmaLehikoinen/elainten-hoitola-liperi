<x-kurssit::layouts.public title="Kurssit">
    <style>
        .kurssit-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        @media (min-width: 640px) {
            .kurssit-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 900px) {
            .kurssit-grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>

    <div x-data="{ openCourseId: null }">
        <h1 style="font-family: var(--brand-heading-font); font-size: 26px; color: var(--brand-text); margin-bottom: 24px;">
            Kurssit
        </h1>

        @if ($courses->isEmpty())
            <p style="color: var(--brand-text); opacity:0.7;">Ei tällä hetkellä avoimia kursseja.</p>
        @endif

                     <div class="kurssit-grid">   
            @foreach ($courses as $course)
                @php
                    $thumb = null;
                    if ($course->presentation_type === 'blocks' && ! empty($course->content_blocks)) {
                        $first = $course->content_blocks[0];
                        if (($first['type'] ?? null) === 'image_full') {
                            $thumb = $first['path'];
                        }
                    }
                @endphp

                <div style="background:white; border-radius: var(--brand-radius, 12px); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08); cursor:pointer; display:flex; flex-direction:column;"
                    @click="openCourseId = {{ $course->id }}">
                    @if ($thumb)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($thumb) }}" style="width:100%; aspect-ratio:1/1; object-fit:cover; display:block;">
                    @endif

                    <div style="padding:16px 18px; flex:1; display:flex; flex-direction:column;">
                        <h2 style="font-size:16px; font-weight:600; color: var(--brand-text); margin:0 0 6px; font-family: var(--brand-heading-font);">
                            {{ $course->name }}
                        </h2>

                        @if ($course->short_description)
                            <p style="color: var(--brand-text); opacity:0.85; font-size:13px; margin:0 0 8px;">{{ $course->short_description }}</p>
                        @endif

                        @if ($course->starts_at)
                            <p style="color: var(--brand-accent); opacity:0.7; font-size:13px; margin:0 0 4px;">{{ $course->starts_at->format('d.m.Y H:i') }}</p>
                        @endif

                        <p style="color: var(--brand-accent); opacity:0.7; font-size:13px; margin:0 0 12px;">
                            {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
                            · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
                        </p>

                                                    <div style="margin-top:auto;">
                            @if ($course->isFull())
                                <span style="display:inline-block; padding:6px 14px; border-radius: var(--brand-radius, 6px); background: var(--brand-background); color: var(--brand-accent); font-size:13px;">Täynnä</span>
                            @elseif ($course->isRegistrationClosed())
                                <span style="display:inline-block; padding:6px 14px; border-radius: var(--brand-radius, 6px); background: var(--brand-background); color: var(--brand-accent); font-size:13px;">Ilmoittautuminen suljettu</span>
                            @else
                                <button type="button" class="btn-brand" @click.stop="openCourseId = {{ $course->id }}"
                                    style="border:none; padding:9px 18px; border-radius: var(--brand-radius, 8px); font-size:13px; font-weight:600; cursor:pointer;">
                                    Ilmoittaudu
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

                   @if ($onlineGiftCardsEnabled)
            <div style="text-align:center; margin-top:32px;">
                <button type="button" onclick="window.location.href='{{ route('kurssit.public.gift-card.show') }}'"
                    style="background:white; border:1px solid var(--brand-secondary); color: var(--brand-text); padding:12px 24px; border-radius: var(--brand-radius, 8px); font-size:14px; font-weight:600; cursor:pointer;">
                    Osta lahjakortti
                </button>
            </div>
        @endif 

        <div x-show="openCourseId !== null" x-cloak
            style="position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:50; display:flex; align-items:center; justify-content:center; padding:16px;"
            @click.self="openCourseId = null">
            <div style="background:white; border-radius: var(--brand-radius, 16px); max-width:800px; width:100%; max-height:92vh; overflow:hidden; position:relative;">
                <button type="button" @click="openCourseId = null"
                    style="position:absolute; top:12px; right:12px; z-index:2; background:white; border:none; border-radius:50%; width:32px; height:32px; font-size:18px; cursor:pointer; box-shadow:0 1px 4px rgba(0,0,0,0.2);">✕</button>
                <iframe :src="openCourseId ? '/kurssit/' + openCourseId + '/ilmoittaudu' : ''" style="width:100%; height:85vh; border:none; display:block;"></iframe>
            </div>
        </div>
    </div>
</x-kurssit::layouts.public>