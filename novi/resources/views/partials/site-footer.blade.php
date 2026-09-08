{{--
    Jaettu footer kaikille julkisille sivuille (Missukka, Sydänpolku, tulevat sivut).
    Käyttö: @include('partials.site-footer', [
        'company'          => $company,
        'footerBg'         => 'väri',
        'footerText'       => 'väri',
        'footerAccent'     => 'väri (brändi-ikoni)',
        'footerBrandIcon'  => raaka SVG-merkkijono (stroke="currentColor"),
        'footerBrandName'  => 'Yrityksen nimi',
        'footerTagline'    => 'teksti copyright-rivin lopussa',
        'footerBorderSoft' => 'rgba(...) some-ikonin reuna',
        'footerMuted'      => 'rgba(...) copyright-teksti',
        'footerDivider'    => 'rgba(...) viiva copyrightin yllä',
    ])
--}}
@php
    $sfAddress = $company->settings['address'] ?? null;
    $sfFacebook = $company->settings['facebook_url'] ?? null;
    $sfInstagram = $company->settings['instagram_url'] ?? null;
    $footerBrandName = $company->settings['official_name'] ?? $company->name;
    $sfMapQuery = $sfAddress;
@endphp
<div class="site-footer" id="yhteystiedot" style="background-color: {{ $footerBg }};">
    <div class="site-footer-container">
        <div class="site-footer-layout">
            <div class="site-footer-info">
                <div class="site-footer-brand" style="color: {{ $footerText }};">
                    <span class="site-footer-brand-icon" style="color: {{ $footerAccent }};">{!! $footerBrandIcon !!}</span>
                    {{ $footerBrandName }}
                </div>

                @if ($company->phone)
                    <p class="site-footer-line"><a href="tel:{{ preg_replace('/\s+/', '', $company->phone) }}" style="color: {{ $footerText }};">{{ $company->phone }}</a></p>
                @endif

                @if ($sfAddress)
                    <p class="site-footer-line" style="color: {{ $footerText }};">{{ $sfAddress }}</p>
                @endif

                @if ($company->email)
                    <p class="site-footer-line"><a href="mailto:{{ $company->email }}" style="color: {{ $footerText }};">{{ $company->email }}</a></p>
                @endif

                @if ($sfFacebook || $sfInstagram)
                    <div class="site-footer-social-icons">
                        @if ($sfFacebook)
                            <a href="{{ $sfFacebook }}" target="_blank" rel="noopener" style="border-color: {{ $footerBorderSoft }}; color: {{ $footerAccent }};">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M14 9h3V6h-3c-2 0-3.5 1.5-3.5 3.5V11H8v3h2.5v6h3v-6H16l.5-3h-3V9.8c0-.5.2-.8.9-.8Z" /></svg>
                            </a>
                        @endif
                        @if ($sfInstagram)
                            <a href="{{ $sfInstagram }}" target="_blank" rel="noopener" style="border-color: {{ $footerBorderSoft }}; color: {{ $footerAccent }};">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="4" y="4" width="16" height="16" rx="4.5" /><circle cx="12" cy="12" r="3.6" /><circle cx="16.2" cy="7.8" r="0.6" fill="currentColor" stroke="none" /></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            @if ($sfMapQuery)
                <div class="site-footer-map-thumb">
                    <iframe
                        src="https://www.google.com/maps?q={{ urlencode($sfMapQuery) }}&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($sfMapQuery) }}" target="_blank" rel="noopener" aria-label="Avaa reittiohjeet Google Mapsissa"></a>
                </div>
            @endif
        </div>

        <p class="site-footer-copy" style="border-top-color: {{ $footerDivider }}; color: {{ $footerMuted }};">
            © {{ date('Y') }} {{ $footerBrandName }}@if($footerTagline) · {{ $footerTagline }}@endif
        </p>
    </div>
</div>

<style>
    .site-footer { padding: 40px 0; }
    .site-footer-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    .site-footer-layout { display: flex; flex-direction: column; gap: 24px; }
    @media (min-width: 640px) {
            .site-footer-layout { flex-direction: row; align-items: flex-start; justify-content: flex-start; gap: 48px; }
    }
    .site-footer-info { display: flex; flex-direction: column; gap: 8px; }
    .site-footer-brand { display: flex; align-items: center; gap: 8px; font-family: var(--brand-heading-font, serif); font-weight: 600; font-size: 16px; }
    .site-footer-brand-icon { display: inline-flex; width: 18px; height: 18px; }
    .site-footer-brand-icon svg { width: 100%; height: 100%; }
    .site-footer-line, .site-footer-line a { font-size: 14.5px; margin: 0; text-decoration: none; }
    .site-footer-line a:hover { text-decoration: underline; }
    .site-footer-social-icons { display: flex; gap: 10px; margin-top: 4px; }
    .site-footer-social-icons a { display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: 1px solid; border-radius: 50%; }
    .site-footer-social-icons svg { width: 14px; height: 14px; }
            .site-footer-map-thumb { position: relative; width: 320px; height: 220px; flex-shrink: 0; border-radius: 12px; overflow: hidden; align-self: flex-start; }
    .site-footer-map-thumb iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; pointer-events: none; }
    .site-footer-map-thumb a { position: absolute; inset: 0; }
    .site-footer-copy { margin-top: 28px; padding-top: 18px; border-top: 1px solid; font-size: 13px; text-align: center; }
</style>