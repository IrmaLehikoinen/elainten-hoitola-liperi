<x-kurssit::layouts.public title="Maksu peruttu">
    <div style="text-align:center; padding: 48px 32px; background:white; border-radius:12px;">
        <h1 style="font-family: var(--brand-heading-font, serif); font-size: 24px; font-weight:700; color: var(--brand-text, #2A3428); margin-bottom:12px;">
            Maksu peruttu
        </h1>

        <p style="color:#5A5A5A; font-size:15px; line-height:1.6; margin-bottom:32px;">
            Ilmoittautumistasi ei vahvistettu. Voit yrittää uudelleen milloin tahansa.
        </p>

        <button type="button" onclick="window.location.href='{{ route('kurssit.public.index') }}'"
            style="background: var(--brand-primary, #3F4F3A); color:white; border:none; padding:14px 32px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer;">
            Takaisin kursseihin
        </button>
    </div>
</x-kurssit::layouts.public>