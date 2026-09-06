<style>
    /* ===== Sydänpolku-brändin yhteiset tyylit — käytössä kaikilla julkisilla sivuilla ===== */
    .sp-page {
        --sp-rose: var(--brand-accent, #80107A);
        --sp-rose-soft: var(--brand-accent-soft, #C4DD5E);
        --sp-gold: var(--brand-warm, #B49170);
        --sp-gold-light: var(--brand-warm-light, #C2AF6F);
        width: 100vw;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
        overflow-x: hidden;
    }
    .sp-bg-cream { background-color: var(--brand-background); }
    .sp-bg-beige { background-color: #F8F5EE; }
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
    .sp-btn-cta:hover { opacity: 0.9; transform: translateY(-1px); }
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

    .sp-section-heading { max-width: 46ch; margin: 0 0 44px; }
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
    .sp-section-heading.sp-center { margin-left: auto; margin-right: auto; text-align: center; }
    .sp-section-heading.sp-center h2 { justify-content: center; }
    .sp-section-heading.sp-accent h2 { color: var(--sp-rose) !important; }

    /* Kurssikortit */
    .sp-course-card { display: flex; flex-direction: column; }
    .sp-course-card .sp-course-img-wrap { border-radius: 18px; overflow: hidden; margin-bottom: 20px; }
    .sp-course-card img {
        width: 100%;
        aspect-ratio: 4/3;
        object-fit: cover;
        display: block;
        transition: transform .5s ease;
    }
    .sp-course-card:hover img { transform: scale(1.04); }
    .sp-course-card h3 {
        font-family: var(--brand-heading-font);
        font-weight: 600;
        font-size: 19px;
        color: var(--brand-text);
        margin: 0 0 10px;
    }
    .sp-course-meta-row { display: flex; flex-wrap: wrap; gap: 4px 14px; margin-bottom: 10px; }
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

    /* Modaalit (rekisteröinti-iframe / tietolaatikot) */
    .sp-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(42,52,40,0.55);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .sp-modal-box {
        background: var(--brand-background);
        border-radius: 20px;
        max-width: 460px;
        width: 100%;
        padding: 36px;
        position: relative;
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

    @media (max-width: 639px) {
        .sp-btn-cta { width: 100%; justify-content: center; padding: 15px 24px; }
    }
</style>