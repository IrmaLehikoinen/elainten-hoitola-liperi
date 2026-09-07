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
            --mh-primary: #4F6142;
            --mh-primary-dark: #3E4D35;
            --mh-brown: #5C4433;
            --mh-terracotta: #C1692F;
            --mh-paw: #BFCB9A;
            --mh-background: #F5F2E7;
            --mh-background-alt: #EFEBDB;
            --mh-text: #3E3A32;
            --mh-heading-font: 'Playfair Display', serif;
            --mh-body-font: 'Inter', sans-serif;

            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            overflow-x: hidden;
            font-family: var(--mh-body-font);
            color: var(--mh-text);
            background-color: var(--mh-background);
        }

        .mh-container {
            width: min(100% - 48px, 1200px);
            margin: 0 auto;
        }
        @media (min-width: 640px) {
            .mh-container { width: min(100% - 80px, 1200px); }
        }

        .mh-band { padding: 72px 0; }
        @media (min-width: 780px) {
            .mh-band { padding: 108px 0; }
        }
        .mh-bg-cream { background-color: var(--mh-background); }
        .mh-bg-alt { background-color: var(--mh-background-alt); }

        /* ===== NAV ===== */
        .mh-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 24px 0;
        }
        .mh-nav-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: var(--mh-heading-font);
            font-weight: 600;
            font-size: 19px;
            color: var(--mh-brown);
            text-decoration: none;
        }
        .mh-nav-brand svg { width: 22px; height: 22px; }

        /* ===== BUTTONS ===== */
        .mh-btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            background-color: var(--mh-primary);
            color: #fff;
            padding: 15px 30px;
            border-radius: 999px;
            font-family: var(--mh-body-font);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .18s ease, opacity .18s ease;
            text-decoration: none;
        }
        .mh-btn-cta:hover { opacity: 0.92; transform: translateY(-1px); }
        .mh-btn-cta.mh-btn-on-dark {
            background-color: #fff;
            color: var(--mh-primary-dark);
        }
        .mh-btn-text {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            padding: 0;
            font-family: var(--mh-body-font);
            font-size: 15px;
            font-weight: 600;
            color: var(--mh-primary);
            cursor: pointer;
            text-decoration: none;
            transition: gap .18s ease;
        }
        .mh-btn-text:hover { gap: 10px; }

        /* ===== HERO ===== */
        .mh-hero { position: relative; padding: 32px 0 64px; }
        @media (min-width: 780px) { .mh-hero { padding: 40px 0 88px; } }
        .mh-paw-watermark {
            position: absolute;
            top: -30px;
            right: -20px;
            width: 280px;
            height: 280px;
            color: var(--mh-paw);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
        }
        @media (min-width: 780px) {
            .mh-paw-watermark { width: 380px; height: 380px; top: -50px; right: -60px; }
        }
        .mh-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            align-items: center;
        }
        @media (min-width: 860px) {
            .mh-hero-grid { grid-template-columns: 46fr 54fr; gap: 64px; }
        }
        .mh-eyebrow {
            font-family: var(--mh-body-font);
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2.2px;
            color: var(--mh-terracotta);
            margin: 0 0 16px;
        }
        .mh-hero-text h1 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(36px, 6vw, 56px);
            line-height: 1.1;
            color: var(--mh-brown);
            margin: 0 0 20px;
        }
        .mh-hero-text p {
            font-family: var(--mh-body-font);
            font-size: 17px;
            line-height: 1.75;
            color: var(--mh-text);
            opacity: 0.85;
            margin: 0 0 32px;
            max-width: 42ch;
        }
        .mh-hero-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 24px;
        }
        .mh-hero-image-wrap { position: relative; }
        .mh-hero-image-wrap img {
            width: 100%;
            border-radius: 22px;
            object-fit: cover;
            aspect-ratio: 4/3;
            display: block;
        }
        @media (min-width: 860px) {
            .mh-hero-image-wrap img { aspect-ratio: 5/4; }
        }

        /* ===== SECTION HEADING ===== */
        .mh-section-heading {
            text-align: center;
            max-width: 52ch;
            margin: 0 auto 56px;
        }
        .mh-section-heading h2 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(28px, 4.4vw, 38px);
            color: var(--mh-brown);
            margin: 0 0 12px;
        }
        .mh-section-heading p {
            font-family: var(--mh-body-font);
            font-size: 15.5px;
            color: var(--mh-text);
            opacity: 0.72;
            margin: 0;
        }
        .mh-section-note {
            text-align: center;
            font-family: var(--mh-body-font);
            font-size: 13.5px;
            color: var(--mh-text);
            opacity: 0.55;
            margin: 40px 0 0;
        }

        /* ===== PALVELUMME ===== */
        .mh-services-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }
        @media (min-width: 640px) {
            .mh-services-grid { grid-template-columns: repeat(2, 1fr); gap: 44px; }
        }
        @media (min-width: 1000px) {
            .mh-services-grid { grid-template-columns: repeat(4, 1fr); gap: 36px; }
        }
        .mh-service-item { text-align: center; }
        .mh-service-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background-color: var(--mh-background-alt);
            margin-bottom: 20px;
        }
        .mh-service-icon img,
        .mh-service-icon svg { width: 28px; height: 28px; }
        .mh-service-item h3 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            font-size: 19px;
            color: var(--mh-brown);
            margin: 0 0 8px;
        }
        .mh-service-item p {
            font-family: var(--mh-body-font);
            font-size: 14.5px;
            line-height: 1.65;
            color: var(--mh-text);
            opacity: 0.78;
            margin: 0;
        }

        /* ===== MIKSI VALITA ===== */
        .mh-highlights-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 36px;
        }
        @media (min-width: 640px) {
            .mh-highlights-grid { grid-template-columns: repeat(2, 1fr); gap: 40px; }
        }
        @media (min-width: 1000px) {
            .mh-highlights-grid { grid-template-columns: repeat(4, 1fr); gap: 32px; }
        }
        .mh-highlight-item { text-align: center; padding: 0 8px; }
        .mh-highlight-item svg { width: 26px; height: 26px; margin-bottom: 14px; }
        .mh-highlight-item h3 {
            font-family: var(--mh-body-font);
            font-weight: 600;
            font-size: 15.5px;
            color: var(--mh-brown);
            margin: 0 0 6px;
        }
        .mh-highlight-item p {
            font-family: var(--mh-body-font);
            font-size: 13.5px;
            line-height: 1.6;
            color: var(--mh-text);
            opacity: 0.72;
            margin: 0;
        }

        /* ===== MINUSTA ===== */
        .mh-about-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            align-items: center;
        }
        @media (min-width: 860px) {
            .mh-about-grid { grid-template-columns: minmax(0, 440px) minmax(0, 1fr); gap: 72px; }
        }
        .mh-about-image img {
            width: 100%;
            border-radius: 20px;
            object-fit: cover;
            aspect-ratio: 4/3.4;
            display: block;
        }
        .mh-about-text .mh-eyebrow { margin-bottom: 14px; }
        .mh-about-text h2 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(26px, 4vw, 34px);
            color: var(--mh-brown);
            margin: 0 0 20px;
        }
        .mh-about-text p {
            font-family: var(--mh-body-font);
            font-size: 15.5px;
            line-height: 1.8;
            color: var(--mh-text);
            opacity: 0.85;
            margin: 0 0 18px;
            max-width: 56ch;
        }
        .mh-about-quote {
            font-family: var(--mh-heading-font);
            font-style: italic;
            font-size: 18px;
            color: var(--mh-primary-dark);
            border-left: 3px solid var(--mh-terracotta);
            padding-left: 18px;
            margin: 28px 0 14px;
        }
        .mh-about-signature {
            font-family: var(--mh-heading-font);
            font-size: 17px;
            color: var(--mh-brown);
        }

        /* ===== KURKISTA ARKEEMME ===== */
        .mh-gallery-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        @media (min-width: 700px) {
            .mh-gallery-grid { grid-template-columns: repeat(3, 1fr); gap: 24px; }
        }
        .mh-gallery-grid img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            border-radius: 18px;
            display: block;
            transition: transform .5s ease;
        }
        .mh-gallery-grid a { display: block; overflow: hidden; border-radius: 18px; }
        .mh-gallery-grid a:hover img { transform: scale(1.04); }

        /* ===== CTA BAND ===== */
        .mh-cta-band {
            background-color: var(--mh-primary-dark);
            color: #fff;
            text-align: center;
        }
        .mh-cta-band h2 {
            font-family: var(--mh-heading-font);
            font-weight: 600;
            letter-spacing: -0.01em;
            font-size: clamp(26px, 4.4vw, 36px);
            margin: 0 0 16px;
        }
        .mh-cta-band p {
            font-family: var(--mh-body-font);
            font-size: 15.5px;
            opacity: 0.85;
            max-width: 46ch;
            margin: 0 auto 32px;
        }

        /* ===== FOOTER ===== */
        .mh-footer { padding: 64px 0 48px; background-color: var(--mh-brown); color: #fff; }
        .mh-footer-top {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 40px;
        }
        .mh-footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--mh-heading-font);
            font-weight: 600;
            font-size: 18px;
        }
        .mh-footer-brand svg { width: 20px; height: 20px; opacity: 0.9; }
        .mh-footer-columns {
            display: flex;
            flex-wrap: wrap;
            gap: 48px;
        }
        .mh-footer h4 {
            font-family: var(--mh-body-font);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            opacity: 0.6;
            margin: 0 0 12px;
            font-weight: 600;
        }
        .mh-footer p, .mh-footer a {
            font-family: var(--mh-body-font);
            font-size: 14.5px;
            color: #fff;
            margin: 0 0 6px;
            text-decoration: none;
        }
        .mh-footer a:hover { text-decoration: underline; }
        .mh-footer-note {
            padding-top: 28px;
            border-top: 1px solid rgba(255,255,255,0.14);
            opacity: 0.7;
            font-size: 13.5px;
        }

        /* ===== SCROLL REVEAL ===== */
        .mh-reveal { opacity: 0; transform: translateY(16px); transition: opacity .7s ease, transform .7s ease; }
        .mh-reveal.mh-revealed { opacity: 1; transform: translateY(0); }

        @media (max-width: 639px) {
            .mh-hero-text p, .mh-about-text p, .mh-service-item p { font-size: 16px; }
            .mh-btn-cta { width: 100%; justify-content: center; }
            .mh-hero-actions { flex-direction: column; align-items: flex-start; width: 100%; }
            .mh-hero-actions .mh-btn-cta { width: 100%; }
        }
    </style>

    @php
        // Ison tassun jälki -kuvake — samat sädekehän polut kuin talteen otetussa
        // koodinpätkässä, uudelleenkäytetty tällä sivulla vesileimana.
        $mhPawWatermark = '
            <svg viewBox="0 0 240 240" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <ellipse cx="65" cy="65" rx="24" ry="34" transform="rotate(-20 65 65)" />
                <ellipse cx="112" cy="48" rx="24" ry="36" />
                <ellipse cx="158" cy="60" rx="24" ry="34" transform="rotate(18 158 60)" />
                <ellipse cx="190" cy="95" rx="22" ry="32" transform="rotate(28 190 95)" />
                <path d="M120 105 C78 105 50 137 50 173 C50 203 73 221 103 221 C119 221 131 215 140 205 C150 215 163 221 178 221 C207 221 228 202 228 173 C228 138 199 106 158 106 C143 106 131 111 120 119 C110 111 98 105 120 105Z" />
            </svg>
        ';
        $mhPawSmall = '<svg viewBox="0 0 24 22" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="15.5" rx="5.2" ry="4.2" /><circle cx="5.5" cy="8.5" r="2" /><circle cx="10.3" cy="5.3" r="2" /><circle cx="14.7" cy="5.3" r="2" /><circle cx="19" cy="8.7" r="2" /></svg>';
        $mhHeart = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-primary)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20 C4 14 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 14 12 20 Z" /></svg>';
        $mhHome = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-primary)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 L12 3 L21 10.5" /><path d="M5.5 9v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V9" /></svg>';
        $mhTree = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-primary)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 L4 12h4L4 20h16l-4-8h4Z" /><path d="M12 20v2" /></svg>';
        $mhPaws = '<svg viewBox="0 0 24 22" fill="none" stroke="var(--mh-primary)" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="15.5" rx="5.2" ry="4.2" /><circle cx="5.5" cy="8.5" r="2" /><circle cx="10.3" cy="5.3" r="2" /><circle cx="14.7" cy="5.3" r="2" /><circle cx="19" cy="8.7" r="2" /></svg>';
    @endphp
</head>
<body>
    <div class="mh-page">

        {{-- NAV --}}
        <div class="mh-container">
            <nav class="mh-nav">
                <a href="{{ route('missukka.index') }}" class="mh-nav-brand">
                    {!! str_replace('currentColor', 'var(--mh-primary)', $mhPawSmall) !!}
                    Missukan Lemmikkihoitola
                </a>
                <a href="{{ route('public.booking.start') }}" class="mh-btn-cta">Varaa hoitopaikka</a>
            </nav>
        </div>

        {{-- HERO --}}
        <div class="mh-hero mh-bg-cream">
            <div class="mh-container">
                <div class="mh-paw-watermark">{!! $mhPawWatermark !!}</div>
                <div class="mh-hero-grid">
                    <div class="mh-hero-text">
                        <p class="mh-eyebrow">Lämmin ja turvallinen hoitopaikka lemmikillesi</p>
                        <h1>Missukan Lemmikkihoitola</h1>
                        <p>Kodikas ja rauhallinen hoitopaikka koirille, kissoille, kaneille ja marsuille aivan Liperin maaseudun keskellä — kaukana liikenteen melusta, lähellä lintujen laulua.</p>
                        <div class="mh-hero-actions">
                            <a href="{{ route('public.booking.start') }}" class="mh-btn-cta">Varaa hoitopaikka</a>
                            <a href="#palvelut" class="mh-btn-text">Tutustu palveluihin ↓</a>
                        </div>
                    </div>
                    <div class="mh-hero-image-wrap">
                        <img src="{{ asset('images/missukka/hero-koira-mokki.jpg') }}" alt="Kultainennoutaja juoksemassa niityllä mökin edessä">
                    </div>
                </div>
            </div>
        </div>

        {{-- PALVELUMME --}}
        <div id="palvelut" class="mh-band mh-bg-alt mh-reveal">
            <div class="mh-container">
                <div class="mh-section-heading">
                    <h2>Palvelumme</h2>
                    <p>Joustavaa ja yksilöllistä hoitoa lemmikkisi tarpeisiin.</p>
                </div>

                <div class="mh-services-grid">
                    <div class="mh-service-item">
                        <span class="mh-service-icon">{!! $mhPaws !!}</span>
                        <h3>Päivähoito</h3>
                        <p>Turvallista ja aktiivista päivähoitoa koirille ja kissoille.</p>
                    </div>
                    <div class="mh-service-item">
                        <span class="mh-service-icon">
                            <img src="{{ asset('images/missukka/icon-koti.png') }}" alt="">
                        </span>
                        <h3>Yöhoito</h3>
                        <p>Rauhallinen ja kodinomainen yöhoito lemmikillesi.</p>
                    </div>
                    <div class="mh-service-item">
                        <span class="mh-service-icon">
                            <img src="{{ asset('images/missukka/icon-kuusi.png') }}" alt="">
                        </span>
                        <h3>Pidempi hoito</h3>
                        <p>Myös pidemmät hoitojaksot onnistuvat, esimerkiksi loman ajaksi.</p>
                    </div>
                    <div class="mh-service-item">
                        <span class="mh-service-icon">{!! $mhHeart !!}</span>
                        <h3>Lisäpalvelut</h3>
                        <p>Kysy rohkeasti myös muista tarpeista — autamme mielellämme.</p>
                    </div>
                </div>

                <p class="mh-section-note">Hinnasto ja kesän tarjoukset julkaistaan pian tällä sivulla.</p>
            </div>
        </div>

        {{-- MIKSI VALITA MISSUKKA --}}
        <div class="mh-band mh-bg-cream mh-reveal">
            <div class="mh-container">
                <div class="mh-section-heading">
                    <h2>Miksi valita Missukka?</h2>
                    <p>Lemmikkisi hyvinvointi on meille sydämen asia.</p>
                </div>

                <div class="mh-highlights-grid">
                    <div class="mh-highlight-item">
                        {!! $mhHome !!}
                        <h3>Kodikas ympäristö</h3>
                        <p>Lemmikit asuvat kodinomaisessa ympäristössä, ei häkeissä.</p>
                    </div>
                    <div class="mh-highlight-item">
                        {!! $mhHeart !!}
                        <h3>Yksilöllinen hoito</h3>
                        <p>Jokainen lemmikki kohdataan omana itsenään ja saa juuri sille sopivaa huomiota.</p>
                    </div>
                    <div class="mh-highlight-item">
                        {!! $mhPaws !!}
                        <h3>Kokemus eläimistä</h3>
                        <p>Pitkä kokemus koirien ja kissojen hoidosta sekä aito rakkaus eläimiin vuodesta 1997.</p>
                    </div>
                    <div class="mh-highlight-item">
                        {!! $mhTree !!}
                        <h3>Rauhallinen maaseutumiljöö</h3>
                        <p>Sijaitsemme 10 km Liperistä, keskellä metsää ja hiljaisuutta.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MINUSTA --}}
        <div class="mh-band mh-bg-alt mh-reveal">
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

        {{-- KURKISTA ARKEEMME --}}
        <div class="mh-band mh-bg-cream mh-reveal">
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
                    <a href="{{ asset('images/missukka/arki-yhdessa.jpg') }}" target="_blank" rel="noopener">
                        <img src="{{ asset('images/missukka/arki-yhdessa.jpg') }}" alt="Koira ja kissa yhdessä sisällä">
                    </a>
                </div>
            </div>
        </div>

        {{-- CTA-BANNI --}}
        <div class="mh-band mh-cta-band mh-reveal">
            <div class="mh-container">
                <h2>Varaa lemmikkillesi kodikas hoitopaikka</h2>
                <p>Ota yhteyttä ja kysy lisää, tai varaa hoitopaikka suoraan verkossa.</p>
                <a href="{{ route('public.booking.start') }}" class="mh-btn-cta mh-btn-on-dark">Varaa hoitopaikka →</a>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="mh-footer">
            <div class="mh-container">
                <div class="mh-footer-top">
                    <div class="mh-footer-brand">
                        {!! str_replace('currentColor', '#fff', $mhPawSmall) !!}
                        Missukan Lemmikkihoitola
                    </div>

                    <div class="mh-footer-columns">
                        <div>
                            <h4>Yhteystiedot</h4>
                            <p><a href="tel:0405371603">040 5371603</a></p>
                            <p><a href="mailto:missukanlemmikkihoitola@gmail.com">missukanlemmikkihoitola@gmail.com</a></p>
                        </div>

                        <div>
                            <h4>Sijainti</h4>
                            <p>Niinikkosaarentie 97, 83100 Liperi</p>
                            <p style="opacity:0.65;">Pohjois-Karjala</p>
                        </div>
                    </div>
                </div>

                <p class="mh-footer-note">© {{ date('Y') }} Missukan Lemmikkihoitola · Sivut rakkaudella eläimille</p>
            </div>
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