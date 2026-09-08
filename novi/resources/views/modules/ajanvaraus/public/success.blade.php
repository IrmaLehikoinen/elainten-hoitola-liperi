<x-ajanvaraus::layouts.public title="Kiitos varauksesta">
    <div style="text-align:center; padding: 48px 32px; background:white; border-radius:12px;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--brand-primary, #3F4F3A);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" style="width:32px;height:32px;">
                <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1 style="font-family: var(--brand-heading-font, serif); font-size: 24px; font-weight:700; color: var(--brand-text, #2A3428); margin-bottom:12px;">
            Kiitos varauksestasi!
        </h1>

        <p style="color:#5A5A5A; font-size:15px; line-height:1.6; margin-bottom:8px;">
            {{ $appointment->treatment->name }} — {{ $appointment->starts_at->translatedFormat('l j.n.Y \k\l\o H:i') }}
        </p>
        <p style="color:#5A5A5A; font-size:15px; line-height:1.6; margin-bottom:32px;">
            Vahvistus on lähetetty osoitteeseen {{ $appointment->email }}.
        </p>

        <button type="button" onclick="window.location.href='{{ route('ajanvaraus.public.book') }}'"
            style="background: var(--brand-primary, #3F4F3A); color:white; border:none; padding:14px 32px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer;">
            Takaisin ajanvaraukseen
        </button>
    </div>
</x-ajanvaraus::layouts.public>