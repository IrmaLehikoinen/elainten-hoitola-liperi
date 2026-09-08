<x-kurssit::layouts.public title="Työhyvinvointipäivät – Sydänpolku">
    <style>
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
        .sp-bg-cream { background-color: var(--brand-background); }
        .sp-bg-beige { background-color: #F8F5EE; }
        .sp-eyebrow {
            font-family: var(--brand-body-font);
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--sp-rose) !important;
            margin: 0 0 14px;
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
        .thy-hero {
            max-width: 720px;
            margin: 0 auto;
            text-align: center;
        }
        .thy-hero h1 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 34px;
            color: var(--brand-text);
            margin: 0 0 18px;
        }
        @media (min-width: 640px) {
            .thy-hero h1 { font-size: 42px; }
        }
        .thy-hero p {
            font-family: var(--brand-body-font);
            font-size: 16px;
            line-height: 1.75;
            color: var(--brand-text);
            opacity: 0.82;
            margin: 0 0 20px;
            max-width: 62ch;
            margin-left: auto;
            margin-right: auto;
        }
        .thy-image-wrap {
            max-width: 560px;
            margin: 48px auto 0;
            border-radius: 20px;
            overflow: hidden;
        }
        .thy-image-wrap img {
            display: block;
            width: 100%;
            height: auto;
        }
        .thy-cta {
            max-width: 560px;
            margin: 0 auto;
            text-align: center;
        }
        .thy-cta h2 {
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 24px;
            color: var(--brand-text);
            margin: 0 0 24px;
        }
    </style>

    <div class="sp-page">

    <div class="sp-container">
        @include('kurssit::partials.nav')
    </div>

    {{-- HERO / INTRO --}}
    <div class="sp-band sp-bg-cream">
        <div class="sp-container">
            <div class="thy-hero">
                <p class="sp-eyebrow">Työyhteisöille</p>
                <h1>Työhyvinvointipäivät</h1>
                <p>
                    Työhyvinvointipäivä on tarkoitettu työyhteisöille, jotka kaipaavat yhteistä hetkeä arjen
                    keskelle — tilaa pysähtyä, hengähtää ja olla läsnä yhdessä ilman kiirettä.
                </p>
                <p>
                    Päivän sisältö suunnitellaan aina teidän työyhteisönne toiveiden ja tarpeiden mukaan.
                    Ohjelmaan voi kuulua esimerkiksi rentoutumista, hengitysharjoituksia, äänimaljojen
                    rauhoittavaa värähtelyä tai muuta hyvinvointia tukevaa tekemistä — tavoitteena on aina
                    yhteinen palautuminen ja mieluisa yhdessäolon hetki. Työhyvinvointipäivä sopii niin
                    pienelle tiimille kuin koko henkilöstölle, ja se voidaan järjestää joko Sydänpolun
                    tiloissa tai teidän omissa tiloissanne.
                </p>
            </div>

            <div class="thy-image-wrap">
                <img src="{{ asset('images/sydanpolku/sydankivi.png') }}" alt="Sydänkivi">
            </div>
        </div>
    </div>

    {{-- YHTEYDENOTTO --}}
    <div class="sp-band sp-bg-beige">
        <div class="sp-container">
            <div class="thy-cta">
                <h2>Suunnitellaan teidän työyhteisöllenne sopiva päivä</h2>
                <button type="button" class="sp-btn-cta" onclick="window.location.href='{{ route('sydanpolku.index') }}#yhteystiedot'">
                    Kysy työhyvinvointipäivästä
                </button>
            </div>
        </div>
    </div>

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
</x-kurssit::layouts.public>