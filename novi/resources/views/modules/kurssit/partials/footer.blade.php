<div class="sp-footer">
    <div class="sp-container">
    <div class="sp-footer-brand">
        <svg viewBox="0 0 24 27" fill="none" stroke="#fff" stroke-width="0.85" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;opacity:0.85;">
            <path d="M12 19 C4 13.5 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 13.5 12 19 Z" />
        </svg>
        Sydänpolku
    </div>

    <div class="sp-footer-columns">
        <div>
            <h4>Yhteystiedot</h4>
            <p><a href="tel:0405371603">040 5371603</a></p>
        </div>

        <div>
            <h4>Sijainti</h4>
            <p>Niinikkosaarentie 97, 83100 Liperi</p>
            <p style="opacity:0.65;">Pohjois-Karjala</p>
        </div>
    </div>

    <p class="sp-footer-link">
        <a href="{{ route('kurssit.public.index') }}">Katso kaikki kurssit ja ilmoittaudu →</a>
    </p>
    </div>

    <style>
        .sp-footer {
            padding: 72px 0 56px;
            background-color: var(--brand-primary);
            color: #fff;
        }
        .sp-footer .sp-container {
            max-width: 1200px;
        }
        @media (min-width: 640px) {
            .sp-footer { padding: 96px 0 72px; }
        }
        .sp-footer-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 17px;
            opacity: 0.92;
            margin-bottom: 44px;
        }
        @media (min-width: 640px) {
            .sp-footer-brand { margin-bottom: 56px; }
        }
        .sp-footer-columns {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
            max-width: 460px;
        }
        @media (min-width: 500px) {
            .sp-footer-columns { grid-template-columns: 1fr 1fr; }
        }
        .sp-footer h4 {
            font-family: var(--brand-body-font);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            opacity: 0.6;
            margin: 0 0 12px;
            font-weight: 600;
        }
        .sp-footer p, .sp-footer a {
            font-family: var(--brand-body-font);
            font-size: 14.5px;
            color: #fff;
            margin: 0 0 6px;
            text-decoration: none;
        }
        .sp-footer a:hover { text-decoration: underline; }
        .sp-footer-link {
            margin-top: 40px;
            padding-top: 28px;
            border-top: 1px solid rgba(255,255,255,0.14);
            opacity: 0.75;
            font-size: 14px;
        }
        @media (min-width: 640px) {
            .sp-footer-link { margin-top: 52px; }
        }
    </style>
</div>