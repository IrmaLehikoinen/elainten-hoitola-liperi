<x-kurssit::layouts.public title="Sydänpolku">
    <style>
        /* ===== Pehmeät lisäsävyt osioiden vuorotteluun (vain logon omat sävyt) ===== */
        .sp-bg-cream { background-color: var(--brand-background); }
        .sp-bg-beige { background-color: #F8F5EE; }

        /* ===== TAUSTAT LEVEÄNÄ, SISÄLTÖ HALLITUSTI KAPEAMPANA ===== */
        /* Värit Sydänpolku-logon virallisista sävyistä, käytössä vain tällä sivulla */
        .sp-page {
            --brand-primary: #7CAB33;
            --sp-rose: #80107A;
            --sp-rose-soft: #C4DD5E;
            --sp-gold: #B49170;
            --sp-gold-light: #C2AF6F;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            overflow-x: hidden;
        }
        .sp-container {
            width: min(100% - 48px, 1280px);
            margin: 0 auto;
        }
        @media (min-width: 640px) {
            .sp-container { width: min(100% - 80px, 1280px); }
        }

        .sp-band {
            position: relative;
            padding: 88px 0;
        }
        @media (min-width: 640px) {
            .sp-band { padding: 116px 0; }
        }

        .sp-band-hero {
            padding: 32px 0 40px;
        }
        @media (min-width: 640px) {
            .sp-band-hero { padding: 40px 0 56px; }
        }

        .sp-eyebrow {
            font-family: var(--brand-body-font);
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--sp-rose) !important;
            margin: 0 0 14px;
        }

        /* ===== HERO ===== */
        .sp-hero-grid {
            position: relative;
            display: grid;
            grid-template-columns: 1fr;
            gap: 36px;
            align-items: center;
        }
        @media (min-width: 860px) {
            .sp-hero-grid { grid-template-columns: 42fr 58fr; gap: 64px; }
        }
        .sp-hero-text h1 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(38px, 6.8vw, 64px);
            line-height: 1.08;
            color: var(--brand-text);
            margin: 0 0 18px;
        }
        .sp-hero-text h1 .sp-inline-icon {
            display: inline-block;
            margin-left: 6px;
            vertical-align: middle;
            opacity: 0.5;
        }
        .sp-inline-icon svg { stroke-width: 0.9 !important; }
        .sp-hero-text p {
            font-family: var(--brand-body-font);
            font-size: 16.5px;
            line-height: 1.75;
            color: var(--brand-text);
            opacity: 0.82;
            margin: 0 0 30px;
            max-width: 40ch;
        }
        .sp-btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            background-color: var(--brand-primary);
            color: #fff;
            padding: 14px 28px;
            border-radius: 999px;
            font-family: var(--brand-body-font);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .18s ease, opacity .18s ease;
        }
        .sp-btn-cta:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .sp-btn-text {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            padding: 0;
            font-family: var(--brand-body-font);
            font-size: 14.5px;
            font-weight: 600;
            color: var(--brand-primary);
            cursor: pointer;
            transition: gap .18s ease;
        }
        .sp-btn-text:hover { gap: 10px; }

        .sp-hero-image-wrap {
            position: relative;
            margin-top: 20px;
        }
        @media (min-width: 860px) {
            .sp-hero-image-wrap {
                margin-top: 0;
                max-width: 660px;
                margin-left: auto;
            }
        }
        .sp-hero-image-wrap img {
            width: 100%;
            border-radius: 22px;
            object-fit: cover;
            aspect-ratio: 16/10;
            display: block;
        }
        @media (min-width: 860px) {
            .sp-hero-image-wrap img {
                aspect-ratio: auto;
                height: clamp(360px, 30vw, 420px);
            }
        }
        .sp-heart-watermark {
            position: absolute;
            opacity: 0.07;
            pointer-events: none;
            z-index: 0;
        }
        .sp-hero-grid,
        .sp-about-grid {
            position: relative;
            z-index: 1;
        }

        /* ===== SECTION HEADINGS ===== */
        .sp-section-heading {
            max-width: 46ch;
            margin: 0 0 44px;
        }
        .sp-section-heading h2 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(30px, 4.8vw, 44px);
            color: var(--brand-text);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sp-section-heading h2 .sp-inline-icon { opacity: 0.5; }
                .sp-section-pretitle {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: clamp(18px, 1.6vw, 20px);
            color: var(--sp-rose);
            letter-spacing: 0.03em;
            margin: 0 0 16px;
        }
        .sp-section-heading.sp-center {
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
        .sp-section-heading.sp-center h2 {
            justify-content: center;
        }
        .sp-section-heading.sp-accent h2 {
            color: var(--sp-rose) !important;
        }

        /* ===== TULEVAT KURSSIT (kolme yhtä leveää korttia rinnakkain) ===== */
        .sp-course-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
            max-width: 1240px;
            margin: 0 auto;
        }
        @media (min-width: 700px) {
            .sp-course-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
        }
        .sp-course-card {
            display: flex;
            flex-direction: column;
        }
        .sp-course-card .sp-course-img-wrap {
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .sp-course-card img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            display: block;
            transition: transform .5s ease;
        }
        .sp-course-card:hover img {
            transform: scale(1.04);
        }
        .sp-course-card h3 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 19px;
            color: var(--brand-text);
            margin: 0 0 10px;
        }
        .sp-course-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 4px 14px;
            margin-bottom: 10px;
        }
        .sp-course-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: var(--brand-body-font);
            font-size: 13.5px;
            color: var(--brand-text);
            opacity: 0.68;
        }
        .sp-course-price {
            font-family: var(--brand-body-font);
            font-size: 14px;
            font-weight: 600;
            color: var(--sp-gold-light);
            margin: 0 0 16px;
        }
        .sp-tag-muted {
            display: inline-block;
            font-family: var(--brand-body-font);
            font-size: 13px;
            font-weight: 600;
            color: var(--sp-rose) !important;
            padding: 6px 0;
        }

        /* ===== PALVELUT (kolme palstaa, kaksi riviä, yhtenäinen koko) ===== */
        .sp-services-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 44px;
            max-width: 1150px;
            margin: 0 auto;
        }
        @media (min-width: 700px) {
            .sp-services-grid {
                grid-template-columns: repeat(3, minmax(0, 330px));
                justify-content: center;
                column-gap: 80px;
                row-gap: 56px;
            }
        }
        .sp-service-item {
            max-width: 330px;
        }
        .sp-service-item .sp-service-icon {
            display: inline-flex;
            margin-bottom: 16px;
        }
        .sp-service-item .sp-service-icon svg {
            width: 26px !important;
            height: 26px !important;
        }
        .sp-service-item h3 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 19px;
            color: var(--brand-text);
            margin: 0 0 8px;
        }
                .sp-service-item p {
            font-family: var(--brand-body-font);
            font-size: 14.5px;
            line-height: 1.65;
            color: var(--brand-text);
            opacity: 0.78;
            margin: 0 0 14px;
        }

                /* ===== VARAA AIKA -NOSTO ===== */
        .sp-booking-cta {
            max-width: 640px;
            margin: 96px auto 0;
            background: #F1F0E4;
            border-radius: 20px;
            padding: 40px 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 18px;
        }
        .sp-booking-cta-text h3 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: clamp(28px, 3vw, 34px);
            color: var(--brand-text);
            margin: 0 0 10px;
        }
        .sp-booking-cta-text p {
            font-family: var(--brand-body-font);
            font-size: 16px;
            line-height: 1.6;
            color: var(--brand-text);
            opacity: 0.75;
            max-width: 46ch;
            margin: 0 auto;
        }
        .sp-booking-cta-action {
            flex-shrink: 0;
        }
        @media (max-width: 699px) {
            .sp-booking-cta {
                padding: 32px 24px;
            }
            .sp-booking-cta-action .sp-btn-cta {
                width: 100%;
                justify-content: center;
            }
        }
        /* ===== MODAALI ===== */
            .sp-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(42,52,40,0.55);
            z-index: 50;
        }
        .sp-modal-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--brand-background);
            border-radius: 20px;
            max-width: 460px;
            width: calc(100% - 40px);
            max-height: 80vh;
            overflow-y: auto;
            padding: 36px;
        }
        .sp-modal-close {
            position: absolute;
            top: 16px;
            right: 18px;
            background: none;
            border: none;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            color: var(--brand-text);
            opacity: 0.5;
        }
        .sp-modal-box h3 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 21px;
            color: var(--brand-text);
            margin: 0 0 14px;
        }
                .sp-modal-box p {
            font-family: var(--brand-body-font);
            font-size: 15px;
            line-height: 1.75;
            color: var(--brand-text);
            opacity: 0.85;
            margin: 0;
        }
        .sp-modal-box-wide {
            max-width: 560px;
        }
        .sp-treatment-list {
            margin-top: 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .sp-treatment-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        .sp-treatment-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .sp-treatment-row strong {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 21px;
            color: var(--sp-rose);
        }
        .sp-treatment-row p {
            font-family: var(--brand-body-font);
            font-size: 13.5px;
            line-height: 1.6;
            color: var(--brand-text);
            opacity: 0.75;
            margin: 4px 0 0;
        }
        .sp-treatment-row .sp-btn-text {
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ===== SYDÄNPOLKU ESITTELY (kuva ja teksti lähellä toisiaan) ===== */
        .sp-about-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }
        @media (min-width: 900px) {
            .sp-about-grid {
                grid-template-columns: minmax(0, 520px) minmax(0, 520px);
                justify-content: center;
                align-items: start;
                gap: 84px;
            }
        }
        .sp-about-image img {
            width: 100%;
            border-radius: 20px;
            object-fit: cover;
            aspect-ratio: 3/3.4;
            display: block;
        }
        @media (min-width: 900px) {
            .sp-about-image { margin-top: 36px; }
        }
        .sp-about-text h2 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(30px, 4.6vw, 40px);
            color: var(--brand-text);
            margin: 0 0 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sp-about-text h2 .sp-inline-icon { opacity: 0.5; }
        .sp-about-text p {
            font-family: var(--brand-body-font);
            font-size: 16px;
            line-height: 1.8;
            color: var(--brand-text);
            opacity: 0.85;
            margin: 0 0 20px;
            max-width: 52ch;
        }

        /* ===== KOLME YDINASIAA (kapeampi, yhtenäinen vesivärimäinen tausta) ===== */
        .sp-highlights-wrap {
            position: relative;
            max-width: 1160px;
            margin: 56px auto 0;
        }
        @media (min-width: 900px) {
            .sp-highlights-wrap { margin: 72px auto 0; }
        }
        .sp-highlights-wrap::before {
            content: '';
            position: absolute;
            inset: -16px -20px;
            background-image: url('{{ asset('images/sydanpolku/vesivari-salvia.svg') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            z-index: 0;
            pointer-events: none;
        }
        @media (min-width: 900px) {
            .sp-highlights-wrap::before { inset: -28px -36px; }
        }
        .sp-highlights-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }
        @media (min-width: 700px) {
            .sp-highlights-grid { grid-template-columns: repeat(3, 1fr); }
        }
        .sp-highlight-item {
            text-align: center;
            padding: 30px 24px;
            border-top: 1px solid rgba(180,145,112,0.35);
        }
        .sp-highlight-item:first-child {
            border-top: none;
        }
        @media (min-width: 700px) {
            .sp-highlight-item {
                border-top: none;
                border-left: 1px solid rgba(180,145,112,0.35);
                padding: 8px 32px;
            }
            .sp-highlight-item:first-child {
                border-left: none;
            }
        }
        .sp-highlight-icon {
            display: inline-flex;
            margin-bottom: 16px;
        }
        .sp-highlight-icon svg {
            width: 28px !important;
            height: 28px !important;
        }
        .sp-highlight-item p {
            margin: 0;
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 16.5px;
            line-height: 1.4;
            color: var(--brand-text);
            max-width: 22ch;
            margin-left: auto;
            margin-right: auto;
        }

        /* ===== SCROLL REVEAL ===== */
        .sp-reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity .7s ease, transform .7s ease;
        }
        .sp-reveal.sp-revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== MOBIILI: body-tekstin minimikoko ja isommat painikkeet ===== */
        @media (max-width: 639px) {
            .sp-hero-text p,
            .sp-about-text p,
            .sp-service-item p,
            .sp-modal-box p {
                font-size: 16px;
            }
            .sp-btn-cta {
                width: 100%;
                justify-content: center;
                padding: 15px 24px;
            }
        }
    </style>

    {{-- Uudelleenkäytettävät kuvakkeet (hienovaraiset viivapiirrokset) --}}
    @php
        $spLeaf = '
            <svg viewBox="0 0 30 46" fill="none" stroke="var(--brand-primary)" stroke-width="0.85" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;opacity:0.85;">
                <g transform="rotate(30 15 22)">
                    <path d="M15 3 C22 10 25 19 20 28 C18 32 16.5 34 15 34 C13.5 34 12 32 10 28 C5 19 8 10 15 3 Z" />
                    <path d="M15 3 L15 44" />
                    <path d="M15 11 L9 15 M15 11 L21 15" />
                    <path d="M15 18 L8 23 M15 18 L22 23" />
                    <path d="M15 25 L10 29 M15 25 L20 29" />
                </g>
            </svg>
        ';
        $spHeartTail = '
            <svg viewBox="0 0 24 27" fill="none" stroke="var(--sp-rose)" stroke-width="1.05" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;opacity:1;">
                <path d="M12 19 C4 13.5 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 13.5 12 19 Z" />
                <path d="M12 19 C11 21 9.7 22.3 11.2 25" />
                <path d="M12 14.5 C8.7 11.8 7.2 9.5 7.2 7.4 C7.2 6 8.2 5.2 9.3 5.2 C10.4 5.2 11.3 5.9 12 7.1 C12.7 5.9 13.6 5.2 14.7 5.2 C15.8 5.2 16.8 6 16.8 7.4 C16.8 9.5 15.3 11.8 12 14.5 Z" stroke-width="1.05" />
            </svg>
        ';
        $spHeartSimple = '
            <svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.05" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;opacity:1;">
                <path d="M12 20 C4 14 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 14 12 20 Z" />
            </svg>
        ';
        $spSparkle = '
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--sp-rose-soft)" stroke-width="1.05" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px;opacity:1;">
                <path d="M12 2 C12.8 7 13 8 19 9 C13 10 12.8 11 12 16 C11.2 11 11 10 5 9 C11 8 11.2 7 12 2 Z" />
            </svg>
        ';
        $spHeartWatermark = '
            <svg viewBox="0 0 24 22" fill="var(--sp-rose)" stroke="none" style="width:100%;height:100%;display:block;">
                <path d="M12 20 C4 14 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 14 12 20 Z" />
            </svg>
        ';
        $spCalendarIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:14px;height:14px;"><rect x="3.5" y="5" width="17" height="16" rx="2" /><path stroke-linecap="round" d="M3.5 9.5h17M8 3v4M16 3v4" /></svg>';
        $spPinIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11.5a7 7 0 1 0-14 0C5 14.5 12 21 12 21Z" /><circle cx="12" cy="9.5" r="2.3" /></svg>';

        $courseImages = [
            asset('images/sydanpolku/aanimaljat-hoito.png'),
            asset('images/sydanpolku/sydankivi.png'),
            asset('images/sydanpolku/kuivakukat.png'),
        ];

        $spIconChat = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><path d="M3 4h18a1.5 1.5 0 0 1 1.5 1.5v9A1.5 1.5 0 0 1 21 16H9l-5 4v-4H3A1.5 1.5 0 0 1 1.5 14.5v-9A1.5 1.5 0 0 1 3 4Z" /><path d="M9.2 9c-.7-.8-1.9-.8-2.5.1-.6.7-.4 1.7.3 2.4l2.1 2 2.1-2c.7-.7.9-1.7.3-2.4-.6-.9-1.8-.9-2.5-.1Z" /></svg>';
        $spIconTeam = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><circle cx="9" cy="11" r="6" /><circle cx="15" cy="11" r="6" /></svg>';
        $spIconLotusSmall = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><path d="M12 20V13" /><path d="M12 13C8 13 3 9.5 3 4C8 4 12 8 12 13Z" /><path d="M12 13C16 13 21 9.5 21 4C16 4 12 8 12 13Z" /></svg>';
        $spIconBowl = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><path d="M4 14h16" /><path d="M4 14c0 3.3 3.6 5 8 5s8-1.7 8-5" /><path d="M9.5 8c1-1 2-1 3 0M13.5 5.5c1-1 2-1 3 0" /></svg>';
        $spIconPaw = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><ellipse cx="12" cy="15.5" rx="5.2" ry="4.2" /><circle cx="5.5" cy="8.5" r="2" /><circle cx="10.3" cy="5.3" r="2" /><circle cx="14.7" cy="5.3" r="2" /><circle cx="19" cy="8.7" r="2" /></svg>';
        $spIconBook = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><path d="M12 5c-2-1.4-5-1.8-8-1v13c3-.8 6-.4 8 1c2-1.4 5-1.8 8-1V4c-3-.8-6-.4-8 1Z" /><path d="M12 5v13" /></svg>';

                $spIconTouch = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><path d="M8 11V4.5a1.5 1.5 0 0 1 3 0V10" /><path d="M11 10V3.5a1.5 1.5 0 0 1 3 0V10" /><path d="M14 10V5a1.5 1.5 0 0 1 3 0v7" /><path d="M17 12v-2a1.5 1.5 0 0 1 3 0v5c0 3-2 6-6 6h-2c-3 0-4.5-1.5-6-4l-2-3.5c-.6-1 0-2 1-2.2 1-.2 1.7.3 2.2 1.1L8 15" /></svg>';
        $spIconCupping = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><path d="M7 3h10l-1 8a4 4 0 0 1-8 0L7 3Z" /><path d="M9 3V1.5M15 3V1.5" /><path d="M6 19h12" /></svg>';
        $spIconSpark = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--sp-rose)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;opacity:1;"><path d="M12 2v4M12 16v4M4 11h4M16 11h4M6.5 5.5l2.8 2.8M14.7 8.3l2.8-2.8M6.5 16.5l2.8-2.8M14.7 13.7l2.8 2.8" /></svg>';

                $categoryIconList = [$spIconChat, $spIconBowl, $spIconTouch, $spIconCupping, $spIconSpark, $spIconLotusSmall];

            
    @endphp

    <div class="sp-page">

    <div class="sp-container">
        @include('kurssit::partials.nav')
    </div>

    {{-- HERO --}}
    <div class="sp-band sp-band-hero sp-bg-cream sp-reveal">
        <div class="sp-heart-watermark" style="width:320px; height:320px; top:-60px; right:-40px;">{!! $spHeartWatermark !!}</div>
        <div class="sp-container">
        <div class="sp-hero-grid">
            <div class="sp-hero-text">
                <p class="sp-eyebrow">Matalan kynnyksen hyvinvointipalveluita Liperissä</p>
                <h1>Tervetuloa Sydänpolulle<span class="sp-inline-icon">{!! $spHeartSimple !!}</span></h1>
                <p>Löydä hetki itsellesi ja kulje kohti tasapainoa, hyvinvointia ja iloa.</p>
                <button type="button" class="sp-btn-cta" onclick="window.location.href='{{ route('kurssit.public.index') }}'">
                    Tutustu kursseihin {!! str_replace('var(--sp-rose)', '#fff', $spHeartSimple) !!}
                </button>
            </div>
            <div class="sp-hero-image-wrap">
                <img src="{{ asset('images/sydanpolku/aanimalja-kynttila.png') }}" alt="Äänimalja ja kynttilä">
            </div>
        </div>
        </div>
    </div>

    {{-- SYDÄNPOLKU ESITTELY + KOLME YDINASIAA (yksi kokonaisuus) --}}
        <div id="sp-esittely" class="sp-band sp-bg-beige sp-reveal">
        <div class="sp-heart-watermark" style="width:280px; height:280px; top:320px; left:-30px;">{!! $spHeartWatermark !!}</div>
        <div class="sp-container">
        <div class="sp-about-grid">
            <div class="sp-about-image">
                <img src="{{ asset('images/sydanpolku/hero-metsapolku.png') }}" alt="Metsäpolku">
            </div>

            <div class="sp-about-text">
                <h2>Sydänpolku <span class="sp-inline-icon">{!! $spHeartSimple !!}</span></h2>

                <p>
                    Sydänpolku on paikka pysähtymiselle, oivalluksille ja hyvinvoinnin vahvistamiselle. Toiminta on
                    käynnistynyt Liperissä ajatuksesta, ettei avun ja levon äärelle pääseminen saa olla vaikeaa tai
                    kallista — tarjolla on matalan kynnyksen, edullisia terapia- ja hyvinvointipalveluita arjen
                    keskelle.
                </p>
                <p>
                    Kaikkia palveluita yhdistää sama ajatus: hetken pysähtyminen ja läsnäolo saavat aikaan enemmän
                    kuin kiireessä juokseminen. Sydänpolku toimii Liperissä, Pohjois-Karjalassa, ja tervetuloa on
                    jokainen juuri sellaisena kuin on.
                                </p>
            </div>
        </div>

        <div class="sp-highlights-wrap">
            <div class="sp-highlights-grid">
                <div class="sp-highlight-item">
                    <span class="sp-highlight-icon">{!! $spLeaf !!}</span>
                    <p>Hyvinvointia keholle, mielelle ja sielulle</p>
                </div>
                <div class="sp-highlight-item">
                    <span class="sp-highlight-icon">{!! $spHeartTail !!}</span>
                    <p>Pysähtymistä ja läsnäoloa</p>
                </div>
                <div class="sp-highlight-item">
                    <span class="sp-highlight-icon">{!! $spSparkle !!}</span>
                    <p>Yhteisöllisyyttä ja voimaantumista</p>
                </div>
            </div>
        </div>
        </div>
    </div>

    {{-- TULEVAT KURSSIT --}}
    <div class="sp-band sp-bg-cream sp-reveal">
        <div class="sp-container">
        <div class="sp-section-heading sp-center sp-accent">
            <h2>Tulevat kurssit</h2>
        </div>

        @php
            $spPlaceholders = [
                'Luonnonvoimaa arkeen',
                'Hetki hiljaisuudelle',
                'Kosketus ja rauha',
            ];
            $spCourseSlots = $courses->take(3)->values()->all();
            $spMissing = 3 - count($spCourseSlots);
            for ($i = 0; $i < $spMissing; $i++) {
                $spCourseSlots[] = (object) [
                    'is_placeholder' => true,
                    'name' => $spPlaceholders[$i % count($spPlaceholders)],
                ];
            }
        @endphp

        <div class="sp-course-grid">
            @foreach ($spCourseSlots as $course)
                <div class="sp-course-card">
                    <div class="sp-course-img-wrap">
                        <img src="{{ $courseImages[$loop->index % 3] }}" alt="{{ $course->name }}">
                    </div>

                    <h3>{{ $course->name }}</h3>

                    @if (! ($course->is_placeholder ?? false))
                        <div class="sp-course-meta-row">
                            @if ($course->starts_at)
                                <span class="sp-course-meta">{!! $spCalendarIcon !!} {{ $course->starts_at->format('d.m.Y H:i') }}@if ($course->ends_at)–{{ $course->ends_at->format('H:i') }}@endif</span>
                            @endif
                            <span class="sp-course-meta">{!! $spPinIcon !!} Liperi</span>
                        </div>

                        <p class="sp-course-price">
                            {{ $course->price > 0 ? number_format($course->price, 2, ',', ' ').' €' : 'Maksuton' }}
                            · {{ $course->remainingSpots() }} / {{ $course->max_participants }} paikkaa vapaana
                        </p>

                        @if ($course->isFull())
                            <span class="sp-tag-muted">Täynnä</span>
                        @elseif ($course->isRegistrationClosed())
                            <span class="sp-tag-muted">Ilmoittautuminen suljettu</span>
                        @else
                            <button type="button" class="sp-btn-text" onclick="window.location.href='{{ route('kurssit.public.register', $course) }}'">
                                Lue lisää ja ilmoittaudu →
                            </button>
                        @endif
                    @else
                        <div class="sp-course-meta-row">
                            <span class="sp-course-meta">{!! $spPinIcon !!} Liperi</span>
                        </div>
                        <p class="sp-course-price">Lisätään pian</p>
                        <button type="button" class="sp-btn-text" onclick="window.location.href='{{ route('kurssit.public.index') }}'">
                            Katso kurssit →
                        </button>
                    @endif
                </div>
                            @endforeach
        </div>

        <div style="text-align:center; margin-top:48px;">
            <button type="button" class="sp-btn-cta" onclick="window.location.href='{{ route('kurssit.public.index') }}'">
                Katso kaikki kurssit
            </button>
        </div>
        </div>
    </div>

        {{-- PALVELUT --}}
    <div id="palvelut" class="sp-band sp-bg-cream sp-reveal" x-data="{ openCategory: null }">
        <div class="sp-container">
                <div class="sp-section-heading sp-center">
                        <p class="sp-section-pretitle">Palvelut ja ajanvaraus</p>
            <h2>Hyvinvointia keholle, mielelle ja arkeen <span class="sp-inline-icon">{!! $spHeartSimple !!}</span></h2>
        </div>

                <div class="sp-services-grid">
                        @foreach ($treatmentCategories as $i => $category)
                <div class="sp-service-item">
                    <span class="sp-service-icon">{!! $categoryIconList[$i % count($categoryIconList)] !!}</span>
                    <h3>{{ $category->name }}</h3>
                    @if ($category->description)
                        <p>{{ $category->description }}</p>
                    @endif
                    @if ($category->link_url)
                        <button type="button" class="sp-btn-text" onclick="window.location.href='{{ $category->link_url }}'">
                            Lue lisää →
                        </button>
                                        @else
                        <button type="button" class="sp-btn-text" @click="openCategory = {{ $i }}">
                            {{ $category->treatments->contains('is_active', true) ? 'Varaa aika' : 'Ota yhteyttä' }} →
                        </button>
                    @endif
                </div>
                        @endforeach
        </div>

        <div class="sp-booking-cta">
            <div class="sp-booking-cta-text">
                <h3>Varaa aika</h3>
                <p>Valitse sinulle sopiva hoito ja varaa vapaa aika suoraan kalenterista.</p>
            </div>
            <div class="sp-booking-cta-action">
                <button type="button" class="sp-btn-cta" onclick="window.location.href='{{ route('ajanvaraus.public.book') }}'">
                    Varaa aika →
                </button>
            </div>
        </div>
        </div>

        @foreach ($treatmentCategories as $i => $category)
            <div x-show="openCategory === {{ $i }}" x-cloak @click.self="openCategory = null" class="sp-modal-overlay">
                <div class="sp-modal-box sp-modal-box-wide">
                    <button type="button" class="sp-modal-close" @click="openCategory = null">✕</button>
                    <h3>{{ $category->name }}</h3>
                    @if ($category->description)
                        <p>{{ $category->description }}</p>
                    @endif
                    <div class="sp-treatment-list">
                        @forelse ($category->treatments as $treatment)
                            <div class="sp-treatment-row">
                                <div>
                                    <strong>{{ $treatment->name }}</strong>
                                    @if ($treatment->short_description)
                                        <p>{{ $treatment->short_description }}</p>
                                    @endif
                                </div>
                                                                @if ($treatment->is_active)
                                    <a href="{{ route('ajanvaraus.public.book', ['treatment_id' => $treatment->id]) }}" class="sp-btn-text">Varaa aika →</a>
                                @else
                                    <a href="#yhteystiedot" class="sp-btn-text" @click="openCategory = null">Ota yhteyttä →</a>
                                @endif
                            </div>
                        @empty
                            <p>Palveluita lisätään pian.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- YHTEYSTIEDOT --}}
        @include('partials.site-footer', [
        'company' => $company,
        'footerBg' => 'var(--brand-primary)',
        'footerText' => '#ffffff',
        'footerAccent' => '#ffffff',
        'footerBrandIcon' => '<svg viewBox="0 0 24 27" fill="none" stroke="currentColor" stroke-width="0.85" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19 C4 13.5 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 13.5 12 19 Z" /></svg>',
        'footerBrandName' => 'Sydänpolku',
        'footerTagline' => 'Rakkaudella hyvinvoinnille ♡',
        'footerBorderSoft' => 'rgba(255,255,255,0.35)',
        'footerMuted' => 'rgba(255,255,255,0.75)',
        'footerDivider' => 'rgba(255,255,255,0.18)',
    ])

    </div>{{-- /.sp-page --}}

    <script>
        (function () {
            var revealEls = document.querySelectorAll('.sp-reveal');
            if (!('IntersectionObserver' in window)) {
                revealEls.forEach(function (el) { el.classList.add('sp-revealed'); });
                return;
            }
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('sp-revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            revealEls.forEach(function (el) { observer.observe(el); });
        })();
    </script>
</x-kurssit::layouts.public>