<x-kurssit::layouts.public title="Kurssit">
    @include('kurssit::partials.brand-styles')

    <style>
        .sp-kurssit-page {
            --brand-primary: #7CAB33;
            --sp-rose: #80107A;
            --sp-rose-soft: #C4DD5E;
            --sp-gold: #B49170;
            --sp-gold-light: #C2AF6F;
        }

        .sp-back-link {
            margin-bottom: 4px;
        }

        .sp-kurssit-hero {
            padding: 8px 0 32px;
        }
        .sp-kurssit-hero h1 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: clamp(28px, 4.5vw, 38px);
            color: var(--brand-text);
            margin: 0 0 8px;
        }
        .sp-kurssit-hero p {
            font-family: var(--brand-body-font);
            font-size: 15px;
            color: var(--brand-text);
            opacity: 0.72;
            margin: 0;
            max-width: 46ch;
        }

        .sp-kurssit-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
        }
        @media (min-width: 640px) {
            .sp-kurssit-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 900px) {
            .sp-kurssit-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .sp-course-card {
            background: #fff;
            border: 1px solid rgba(42,52,40,0.08);
            border-radius: var(--brand-radius, 16px);
            box-shadow: 0 1px 4px rgba(42,52,40,0.05);
            padding: 18px 18px 20px;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .sp-course-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(42,52,40,0.09);
        }
        .sp-course-card .sp-course-img-wrap { margin-bottom: 16px; }

        .sp-course-placeholder {
            width: 100%;
            aspect-ratio: 4/3;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--sp-rose-soft, #C4DD5E) 0%, var(--brand-background) 100%);
        }
        .sp-course-placeholder svg {
            width: 34px;
            height: 34px;
            opacity: 0.55;
        }

        .sp-course-desc {
            font-family: var(--brand-body-font);
            font-size: 13.5px;
            line-height: 1.55;
            color: var(--brand-text);
            opacity: 0.78;
            margin: 0 0 12px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .sp-course-badge {
            display: inline-block;
            padding: 7px 16px;
            border-radius: 999px;
            background: var(--brand-background);
            color: var(--brand-text);
            opacity: 0.75;
            font-family: var(--brand-body-font);
            font-size: 13px;
            font-weight: 600;
        }

        .sp-gift-cta {
            margin-top: 48px;
            padding: 36px 32px;
            border-radius: var(--brand-radius, 16px);
            background: #F8F5EE;
            text-align: center;
        }
        .sp-gift-cta h3 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 21px;
            color: var(--brand-text);
            margin: 0 0 8px;
        }
        .sp-gift-cta p {
            font-family: var(--brand-body-font);
            font-size: 14.5px;
            color: var(--brand-text);
            opacity: 0.75;
            margin: 0 0 20px;
        }

        .sp-empty {
            text-align: center;
            padding: 40px 0;
            font-family: var(--brand-body-font);
            color: var(--brand-text);
            opacity: 0.65;
        }
    </style>

    @include('kurssit::partials.nav')

    <div class="sp-back-link">
        <a href="{{ route('sydanpolku.index') }}" class="sp-btn-text">&larr; Takaisin verkkosivulle</a>
    </div>

    <div class="sp-kurssit-page" x-data="{ openCourseId: null }">
        <div class="sp-kurssit-hero">
            <h1>Kurssit</h1>
            <p>Hetkiä pysähtymiseen, hyvinvointiin ja uuden kokemiseen.</p>
        </div>

        @if ($courses->isEmpty())
            <p class="sp-empty">Ei tällä hetkellä avoimia kursseja.</p>
        @else
            <div class="sp-kurssit-grid">
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

                    <div class="sp-course-card" @click="openCourseId = {{ $course->id }}">
                        <div class="sp-course-img-wrap">
                            @if ($thumb)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($thumb) }}"
                                     style="width:100%; aspect-ratio:4/3; border-radius:14px; object-fit:cover; display:block;">
                            @else
                                <div class="sp-course-placeholder">
                                    <svg viewBox="0 0 24 27" fill="none" stroke="var(--brand-primary)" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 19 C4 13.5 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 13.5 12 19 Z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <h3>{{ $course->name }}</h3>

                        @if ($course->short_description)
                            <p class="sp-course-desc">{{ $course->short_description }}</p>
                        @endif

                        <div class="sp-course-meta-row">
                            @if ($course->starts_at)
                                <span class="sp-course-meta">{{ $course->starts_at->format('d.m.Y H:i') }}@if ($course->ends_at)–{{ $course->ends_at->format('H:i') }}@endif</span>
                            @endif
                        </div>

                        <p class="sp-course-price">
                            {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
                        </p>

                        <div class="sp-course-meta-row" style="margin-bottom:16px;">
                            <span class="sp-course-meta">{{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana</span>
                        </div>

                        <div style="margin-top:auto;">
                            @if ($course->isFull())
                                <span class="sp-course-badge">Täynnä</span>
                            @elseif ($course->isRegistrationClosed())
                                <span class="sp-course-badge">Ilmoittautuminen suljettu</span>
                            @else
                                <button type="button" class="sp-btn-cta" @click.stop="openCourseId = {{ $course->id }}">
                                    Ilmoittaudu
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($onlineGiftCardsEnabled)
            <div class="sp-gift-cta">
                <h3>Anna aikaa ja hyvää oloa lahjaksi</h3>
                <p>Sydänpolun lahjakortilla lahjan saaja voi valita itselleen sopivan hetken.</p>
                <button type="button" class="sp-btn-cta" onclick="window.location.href='{{ route('kurssit.public.gift-card.show') }}'">
                    Osta lahjakortti
                </button>
            </div>
        @endif

        @include('kurssit::partials.powered-by-novi')

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