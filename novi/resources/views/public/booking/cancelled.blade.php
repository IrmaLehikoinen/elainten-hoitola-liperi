<x-layouts.public :step="4" :total-steps="5">
    <div class="public-card" style="text-align:center; padding: 48px 32px;">
        <h1 style="font-family: var(--brand-heading-font); font-size: 24px; font-weight:700; color: var(--brand-text); margin-bottom:12px;">
            Maksu peruttiin
        </h1>
        <p style="color:#5A5A5A; font-size:15px; line-height:1.6; margin-bottom:32px;">
            Varaustasi ei vielä vahvistettu. Voit yrittää maksua uudelleen tai aloittaa varauksen alusta.
        </p>

        <button type="button" onclick="window.location.href='{{ route('public.booking.start') }}'"
            style="background: var(--brand-primary); color:white; border:none; padding:14px 32px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; font-family: var(--brand-body-font);">
            Takaisin etusivulle
        </button>
    </div>
</x-layouts.public>