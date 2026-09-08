<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Missukan Lemmikkihoitola – Kodikas hoitopaikka lemmikillesi Liperissä</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:600,700|inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .mh-page {
            --mh-bg: #FAF8F3;
            --mh-bg-accent: #EBF1D8;
            --mh-bg-warm: #F3EEE6;
            --mh-terracotta: #C57137;
            --mh-terracotta-dark: #C3671D;
            --mh-brown: #613E32;
            --mh-sage: #586A4D;
            --mh-text: #5E554F;
            --mh-heading-font: 'Playfair Display', serif;
            --mh-body-font: 'Inter', sans-serif;

            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            overflow-x: hidden;
            font-family: var(--mh-body-font);
            color: var(--mh-text);
            background-color: var(--mh-bg);
        }

        .mh-container {
            width: min(100% - 48px, 1200px);
            margin: 0 auto;
        }
        @media (min-width: 640px) {
            .mh-container { width: min(100% - 80px, 1200px); }
        }

        .mh-band { padding: 52px 0; }
        @media (min-width: 780px) {
            .mh-band { padding: 76px 0; }
        }
        .mh-bg-cream { background-color: var(--mh-bg); }
        .mh-bg-accent { background-color: var(--mh-bg-accent); }
        .mh-bg-warm { background-color: var(--mh-bg-warm); }

        /* ===== NAV ===== */
        .mh-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 22px 0;
        }
        .mh-nav-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: var(--mh-heading-font);
            font-weight: 600;
            font-size: 18px;
            color: var(--mh-brown);
            text-decoration: none;
            flex-shrink: 0;
        }
        .mh-nav-brand svg { width: 20px; height: 20px; }
        .mh-nav-links {
            display: none;
            align-items: center;
            gap: 30px;
        }
        @media (min-width: 900px) {
            .mh-nav-links { display: flex; }
        }
        .mh-nav-links a {
            font-family: var(--mh-body-font);
            font-size: 14.5px;
            font-weight: 600;
            color: var(--mh-brown);
            opacity: 0.8;
            text-decoration: none;
            transition: opacity .15s ease;
        }
        .mh-nav-links a:hover { opacity: 1; }

        /* ===== BUTTONS ===== */
        .mh-btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            background-color: var(--mh-terracotta);
            color: #fff;
            padding: 13px 26px;
            border-radius: 999px;
            font-family: var(--mh-body-font);
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color .18s ease, transform .18s ease;
            text-decoration: none;
            flex-shrink: 0;
        }
        .mh-btn-cta:hover { background-color: var(--mh-terracotta-dark); transform: translateY(-1px); }
        .mh-btn-cta.mh-btn-on-dark {
            background-color: var(--mh-terracotta);
            color: #fff;
        }
        .mh-btn-text {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            padding: 0;
            font-family: var(--mh-body-font);
            font-size: 14.5px;
            font-weight: 600;
            color: var(--mh-sage);
            cursor: pointer;
            text-decoration: none;
            transition: gap .18s ease;
        }
        .mh-btn-text:hover { gap: 10px; }

        /* ===== HERO ===== */
        .mh-hero { position: relative; padding: 28px 0 48px; }
        @media (min-width: 780px) { .mh-hero { padding: 36px 0 64px; } }
        .mh-paw-watermark {
            position: absolute;
            top: -20px;
            right: -10px;
            width: 190px;
            height: 190px;
            opacity: 0.9;
            pointer-events: none;
            z-index: 0;
        }
        .mh-paw-watermark img { width: 100%; height: 100%; object-fit: contain; display: block; }
        @media (min-width: 780px) {
            .mh-paw-watermark { width: 250px; height: 250px; top: -30px; right: -30px; }
        }
        .mh-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 36px;
            align-items: center;
        }
        @media (min-width: 860px) {
            .mh-hero-grid { grid-template-columns: 42fr 58fr; gap: 56px; }
        }
        .mh-eyebrow {
            font-family: var(--mh-body-font);
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2.2px;
            color: var(--mh-terracotta);
            margin: 0 0 14px;
        }
        .mh-hero-text h1 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(34px, 5.6vw, 52px);
            line-height: 1.08;
            color: var(--mh-brown);
            margin: 0 0 16px;
        }
        .mh-hero-text p {
            font-family: var(--mh-body-font);
            font-size: 16.5px;
            line-height: 1.65;
            color: var(--mh-text);
            margin: 0 0 24px;
            max-width: 42ch;
        }
        .mh-hero-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 22px;
            margin-bottom: 28px;
        }
        .mh-hero-perks {
            display: flex;
            flex-wrap: wrap;
            gap: 18px 26px;
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .mh-hero-perks li {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--mh-body-font);
            font-size: 13.5px;
            font-weight: 600;
            color: var(--mh-brown);
        }
        .mh-hero-perks svg { width: 16px; height: 16px; flex-shrink: 0; }
        .mh-hero-image-wrap { position: relative; }
        .mh-hero-image-wrap img {
            width: 100%;
            max-width: 760px;
            height: clamp(340px, 38vw, 480px);
            border-radius: 24px;
            object-fit: cover;
            display: block;
        }

        /* ===== SECTION HEADING ===== */
        .mh-section-heading {
            text-align: center;
            max-width: 52ch;
            margin: 0 auto 40px;
        }
        .mh-section-heading h2 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(26px, 4vw, 34px);
            color: var(--mh-brown);
            margin: 0 0 10px;
        }
        .mh-section-heading p {
            font-family: var(--mh-body-font);
            font-size: 15px;
            color: var(--mh-text);
            margin: 0;
        }
        .mh-section-note {
            text-align: center;
            font-family: var(--mh-body-font);
            font-size: 13.5px;
            color: var(--mh-text);
            opacity: 0.7;
            margin: 32px 0 0;
        }

                /* ===== PALVELUMME ===== */
        .mh-services-wrap {
            position: relative;
        }
        .mh-services-wrap::before {
            content: '';
            position: absolute;
            inset: -20px -24px;
            background-image: url('{{ asset('images/sydanpolku/vesivari-salvia.svg') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            z-index: 0;
            pointer-events: none;
        }
        @media (min-width: 900px) {
            .mh-services-wrap::before { inset: -32px -40px; }
        }
        .mh-services-wrap .mh-services-grid {
            position: relative;
            z-index: 1;
        }
        .mh-services-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
        }
        @media (min-width: 640px) {
            .mh-services-grid { grid-template-columns: repeat(2, 1fr); gap: 32px; }
        }
        @media (min-width: 1000px) {
            .mh-services-grid { grid-template-columns: repeat(4, 1fr); gap: 28px; }
            .mh-service-item:not(:first-child) { border-left: 1px solid rgba(97, 62, 50, 0.12); padding-left: 28px; }
        }
        .mh-service-item { text-align: center; }
        .mh-service-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: var(--mh-bg-accent);
            margin-bottom: 16px;
        }
        .mh-service-icon img,
        .mh-service-icon svg { width: 24px; height: 24px; }
        .mh-service-item h3 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            font-size: 18px;
            color: var(--mh-brown);
            margin: 0 0 6px;
        }
        .mh-service-item p {
            font-family: var(--mh-body-font);
            font-size: 14px;
            line-height: 1.55;
            color: var(--mh-text);
            margin: 0 0 8px;
        }
        .mh-service-item .mh-btn-text { font-size: 13.5px; }

        /* ===== MIKSI VALITA ===== */
        .mh-highlights-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
        }
        @media (min-width: 640px) {
            .mh-highlights-grid { grid-template-columns: repeat(2, 1fr); gap: 32px; }
        }
        @media (min-width: 1000px) {
            .mh-highlights-grid { grid-template-columns: repeat(4, 1fr); gap: 24px; }
        }
        .mh-highlight-item { text-align: center; padding: 0 6px; }
        .mh-highlight-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background-color: #fff;
            margin-bottom: 16px;
        }
        .mh-highlight-badge svg { width: 30px; height: 30px; }
        .mh-highlight-item h3 {
            font-family: var(--mh-body-font);
            font-weight: 600;
            font-size: 15.5px;
            color: var(--mh-brown);
            margin: 0 0 4px;
        }
        .mh-highlight-item p {
            font-family: var(--mh-body-font);
            font-size: 13px;
            line-height: 1.4;
            color: var(--mh-text);
            opacity: 0.75;
            margin: 0;
        }

        /* ===== MINUSTA ===== */
        .mh-about-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            align-items: center;
        }
        @media (min-width: 860px) {
            .mh-about-grid { grid-template-columns: minmax(0, 480px) minmax(0, 1fr); gap: 56px; }
        }
        .mh-about-image img {
            width: 100%;
            max-width: 500px;
            height: clamp(280px, 26vw, 340px);
            border-radius: 18px;
            object-fit: cover;
            display: block;
        }
        .mh-about-text .mh-eyebrow { margin-bottom: 12px; }
        .mh-about-text h2 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(24px, 3.6vw, 32px);
            color: var(--mh-brown);
            margin: 0 0 16px;
        }
        .mh-about-text p {
            font-family: var(--mh-body-font);
            font-size: 15px;
            line-height: 1.7;
            color: var(--mh-text);
            margin: 0 0 14px;
            max-width: 56ch;
        }
        .mh-about-quote {
            font-family: var(--mh-heading-font);
            font-style: italic;
            font-size: 17px;
            color: var(--mh-sage);
            border-left: 3px solid var(--mh-terracotta);
            padding-left: 16px;
            margin: 22px 0 12px;
        }
        .mh-about-signature {
            font-family: var(--mh-heading-font);
            font-size: 16px;
            color: var(--mh-brown);
        }

        /* ===== KURKISTA ARKEEMME ===== */
        .mh-gallery-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        @media (min-width: 700px) {
            .mh-gallery-grid { grid-template-columns: repeat(3, 1fr); gap: 20px; }
        }
        .mh-gallery-grid img {
            width: 100%;
            height: clamp(200px, 18vw, 250px);
            object-fit: cover;
            border-radius: 16px;
            display: block;
            transition: transform .5s ease;
        }
        .mh-gallery-grid a { display: block; overflow: hidden; border-radius: 16px; }
        .mh-gallery-grid a:hover img { transform: scale(1.04); }

                /* ===== CTA BAND ===== */
        .mh-cta-band {
            background-color: var(--mh-sage);
            color: var(--mh-bg);
            text-align: center;
            padding: 32px 0;
        }
        .mh-cta-band h2 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(22px, 3.6vw, 30px);
            color: var(--mh-bg);
            margin: 0 0 8px;
        }
        .mh-cta-band p {
            font-family: var(--mh-body-font);
            font-size: 14.5px;
            color: rgba(250, 248, 243, 0.85);
            max-width: 46ch;
            margin: 0 auto 16px;
        }


        /* ===== SCROLL REVEAL ===== */
        .mh-reveal { opacity: 0; transform: translateY(16px); transition: opacity .7s ease, transform .7s ease; }
        .mh-reveal.mh-revealed { opacity: 1; transform: translateY(0); }

        @media (max-width: 639px) {
            .mh-btn-cta { width: 100%; justify-content: center; }
            .mh-hero-actions { flex-direction: column; align-items: flex-start; width: 100%; }
            .mh-hero-actions .mh-btn-cta { width: 100%; }
        }
    </style>

    @php
        $mhPawSmall = '<svg viewBox="0 0 24 22" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="15.5" rx="5.2" ry="4.2" /><circle cx="5.5" cy="8.5" r="2" /><circle cx="10.3" cy="5.3" r="2" /><circle cx="14.7" cy="5.3" r="2" /><circle cx="19" cy="8.7" r="2" /></svg>';
        $mhHeart = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-sage)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20 C4 14 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 14 12 20 Z" /></svg>';
        $mhHome = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-sage)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 L12 3 L21 10.5" /><path d="M5.5 9v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V9" /></svg>';
        $mhTree = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-sage)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 L4 12h4L4 20h16l-4-8h4Z" /><path d="M12 20v2" /></svg>';
        $mhPaws = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-terracotta)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="15.5" rx="5.2" ry="4.2" /><circle cx="5.5" cy="8.5" r="2" /><circle cx="10.3" cy="5.3" r="2" /><circle cx="14.7" cy="5.3" r="2" /><circle cx="19" cy="8.7" r="2" /></svg>';
        $mhLeaf = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-sage)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20c0-9 6-16 16-16-1 10-7 16-16 16Z" /><path d="M6 18c4-4 7-7 12-10" /></svg>';
        $mhFacebook = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M14 9h3V6h-3c-2 0-3.5 1.5-3.5 3.5V11H8v3h2.5v6h3v-6H16l.5-3h-3V9.8c0-.5.2-.8.9-.8Z" /></svg>';
        $mhInstagram = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="4" y="4" width="16" height="16" rx="4.5" /><circle cx="12" cy="12" r="3.6" /><circle cx="16.2" cy="7.8" r="0.6" fill="currentColor" stroke="none" /></svg>';
    @endphp
</head>
<body>
    <div class="mh-page">

        {{-- NAV --}}
        <div class="mh-container">
            <nav class="mh-nav">
                <a href="{{ route('missukka.index') }}" class="mh-nav-brand">
                    {!! $mhPawSmall !!}
                    Missukan Lemmikkihoitola
                </a>
                <div class="mh-nav-links">
                    <a href="{{ route('missukka.index') }}">Etusivu</a>
                    <a href="#palvelut">Palvelut</a>
                    <a href="#hinnasto">Hinnasto</a>
                    <a href="#galleria">Galleria</a>
                    <a href="#yhteystiedot">Yhteystiedot</a>
                </div>
                <a href="{{ route('public.booking.start') }}" class="mh-btn-cta">Varaa hoitopaikka</a>
            </nav>
        </div>

        {{-- HERO --}}
        <div class="mh-hero mh-bg-cream">
            <div class="mh-container">
                <div class="mh-paw-watermark">
                    <img src="{{ asset('images/missukka/tassunjalki.png') }}" alt="">
                </div>
                <div class="mh-hero-grid">
                    <div class="mh-hero-text">
                        <p class="mh-eyebrow">Lämmin ja turvallinen hoitopaikka lemmikillesi</p>
                        <h1>Missukan Lemmikkihoitola</h1>
                        <p>Kodikas ja rauhallinen hoitopaikka koirille, kissoille, kaneille ja marsuille aivan Liperin maaseudun keskellä — kaukana liikenteen melusta, lähellä lintujen laulua.</p>
                        <div class="mh-hero-actions">
                            <a href="{{ route('public.booking.start') }}" class="mh-btn-cta">Varaa hoitopaikka</a>
                            <a href="#palvelut" class="mh-btn-text">Tutustu palveluihin ↓</a>
                        </div>
                        <ul class="mh-hero-perks">
                            <li>{!! $mhHeart !!} Yksilöllistä hoitoa</li>
                            <li>{!! $mhHome !!} Kodikas ympäristö</li>
                            <li>{!! $mhLeaf !!} Rauhallinen maaseutu</li>
                        </ul>
                    </div>
                    <div class="mh-hero-image-wrap">
                        <img src="{{ asset('images/missukka/arki-yhdessa.jpg') }}" alt="Koira ja kissa lepäämässä yhdessä sisällä">
                    </div>
                </div>
            </div>
        </div>

        {{-- PALVELUMME --}}
        <div id="palvelut" class="mh-band mh-bg-cream mh-reveal">
            <div class="mh-container">
                <div class="mh-section-heading">
                    <h2>Palvelumme</h2>
                    <p>Joustavaa ja yksilöllistä hoitoa lemmikkisi tarpeisiin.</p>
                </div>

                            <div class="mh-services-wrap">
                    <div class="mh-services-grid">
                        <div class="mh-service-item">
                            <span class="mh-service-icon">{!! $mhPaws !!}</span>
                            <h3>Päivähoito</h3>
                            <p>Turvallista ja aktiivista päivähoitoa koirille ja kissoille.</p>
                            <a href="{{ route('public.booking.start') }}" class="mh-btn-text">Varaa aika →</a>
                        </div>
                        <div class="mh-service-item">
                            <span class="mh-service-icon">
                                <img src="{{ asset('images/missukka/icon-koti.png') }}" alt="">
                            </span>
                            <h3>Yöhoito</h3>
                            <p>Rauhallinen ja kodinomainen yöhoito lemmikillesi.</p>
                            <a href="{{ route('public.booking.start') }}" class="mh-btn-text">Varaa aika →</a>
                        </div>
                        <div class="mh-service-item">
                            <span class="mh-service-icon">
                                <img src="{{ asset('images/missukka/icon-kuusi.png') }}" alt="">
                            </span>
                            <h3>Pidempi hoito</h3>
                            <p>Myös pidemmät hoitojaksot onnistuvat, esimerkiksi loman ajaksi.</p>
                            <a href="{{ route('public.booking.start') }}" class="mh-btn-text">Varaa aika →</a>
                        </div>
                        <div class="mh-service-item">
                            <span class="mh-service-icon">{!! $mhHeart !!}</span>
                            <h3>Lisäpalvelut</h3>
                            <p>Kysy rohkeasti myös muista tarpeista — autamme mielellämme.</p>
                            <a href="{{ route('public.booking.start') }}" class="mh-btn-text">Varaa aika →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MINUSTA --}}
        <div class="mh-band mh-bg-warm mh-reveal">
            <div class="mh-container">
                <div class="mh-about-grid">
                    <div class="mh-about-image">
                        <img src="{{ asset('images/missukka/minusta-omistaja.jpg') }}" alt="Minna halaamassa koiraa ja kissaa auringonlaskussa">
                    </div>
                    <div class="mh-about-text">
                        <p class="mh-eyebrow">Minusta</p>
                        <h2>Eläimet ovat aina olleet elämäni osa</h2>
                        <p>Olen Minna, Missukan Lemmikkihoitolan perustaja. Taustallani on pitkä ura ihmisten parissa sosionomina ja lähihoitajana, mutta sydämeni on aina ollut myös eläimissä — ensimmäinen oma koirani tuli jo 18-vuotiaana, ja olen kasvattanut kultaisianoutajia vuodesta 1997 lähtien.</p>
                        <p>Nyt yhdistän nämä kaksi maailmaa: hoidan lemmikkejä samalla lempeydellä ja yksilöllisyydellä, jolla olen aiemmin hoitanut ihmisiä. Asumme metsän keskellä, kaukana liikenteen melusta — täällä kuuluu vain lintujen laulu ja hiljaisuus.</p>
                        <p class="mh-about-quote">"Jokainen lemmikki kohdataan yksilönä, rauhallisesti ja rakkaudella."</p>
                        <p class="mh-about-signature">– Minna</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MIKSI VALITA MISSUKKA --}}
        <div class="mh-band mh-bg-accent mh-reveal">
            <div class="mh-container">
                <div class="mh-section-heading">
                    <h2>Miksi valita Missukka?</h2>
                    <p>Lemmikkisi hyvinvointi on meille sydämen asia.</p>
                </div>

                <div class="mh-highlights-grid">
                    <div class="mh-highlight-item">
                        <span class="mh-highlight-badge">{!! $mhHome !!}</span>
                        <h3>Kodikas ympäristö</h3>
                        <p>Ei häkeissä, vaan kodinomaisesti.</p>
                    </div>
                    <div class="mh-highlight-item">
                        <span class="mh-highlight-badge">{!! $mhHeart !!}</span>
                        <h3>Yksilöllinen hoito</h3>
                        <p>Jokainen lemmikki omana itsenään.</p>
                    </div>
                    <div class="mh-highlight-item">
                        <span class="mh-highlight-badge">{!! $mhPaws !!}</span>
                        <h3>Kokemus eläimistä</h3>
                        <p>Aitoa rakkautta eläimiin vuodesta 1997.</p>
                    </div>
                    <div class="mh-highlight-item">
                        <span class="mh-highlight-badge">{!! $mhTree !!}</span>
                        <h3>Rauhallinen maaseutu</h3>
                        <p>Keskellä metsää ja hiljaisuutta.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- KURKISTA ARKEEMME --}}
        <div id="galleria" class="mh-band mh-bg-cream mh-reveal">
            <div class="mh-container">
                <div class="mh-section-heading">
                    <h2>Kurkista arkeemme</h2>
                    <p>Kuvia onnellisista hoidokeistamme.</p>
                </div>

                <div class="mh-gallery-grid">
                    <a href="{{ asset('images/missukka/arki-koira.jpg') }}" target="_blank" rel="noopener">
                        <img src="{{ asset('images/missukka/arki-koira.jpg') }}" alt="Kultainennoutaja lähikuvassa">
                    </a>
                    <a href="{{ asset('images/missukka/arki-kissa.jpg') }}" target="_blank" rel="noopener">
                        <img src="{{ asset('images/missukka/arki-kissa.jpg') }}" alt="Kissa lepäämässä puutarhassa">
                    </a>
                    <a href="{{ asset('images/missukka/hero-koira-mokki.jpg') }}" target="_blank" rel="noopener">
                        <img src="{{ asset('images/missukka/hero-koira-mokki.jpg') }}" alt="Kultainennoutaja juoksemassa niityllä mökin edessä">
                    </a>
                </div>
            </div>
        </div>

        {{-- CTA-BANNI --}}
        <div class="mh-cta-band mh-reveal">
            <div class="mh-container">
                <h2>Varaa lemmikkillesi kodikas hoitopaikka</h2>
                <p>Ota yhteyttä ja kysy lisää, tai varaa hoitopaikka suoraan verkossa.</p>
                <a href="{{ route('public.booking.start') }}" class="mh-btn-cta mh-btn-on-dark">Varaa hoitopaikka →</a>
            </div>
        </div>

                        {{-- FOOTER --}}
                <div id="yhteystiedot">
                    @include('partials.site-footer', [
                        'company' => $company,
                        'footerBg' => 'var(--mh-bg-warm)',
                        'footerText' => 'var(--mh-brown)',
                        'footerAccent' => 'var(--mh-terracotta)',
                        'footerBrandIcon' => $mhPawSmall,
                        'footerTagline' => 'Sivut rakkaudella eläimille ♡',
                        'footerBorderSoft' => 'rgba(97,62,50,0.25)',
                        'footerMuted' => 'rgba(97,62,50,0.72)',
                        'footerDivider' => 'rgba(97,62,50,0.12)',
                    ])
                </div>

    </div>{{-- /.mh-page --}}

    <script>
        (function () {
            var revealEls = document.querySelectorAll('.mh-reveal');
            if (!('IntersectionObserver' in window)) {
                revealEls.forEach(function (el) { el.classList.add('mh-revealed'); });
                return;
            }
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('mh-revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            revealEls.forEach(function (el) { observer.observe(el); });
        })();
    </script>
</body>
</html>