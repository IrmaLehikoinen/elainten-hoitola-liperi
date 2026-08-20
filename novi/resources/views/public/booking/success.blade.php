<x-layouts.public :step="5" :total-steps="5">
    <div class="public-card" style="text-align:center; padding: 48px 32px;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--brand-primary);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" style="width:32px;height:32px;">
                <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1 style="font-family: var(--brand-heading-font); font-size: 24px; font-weight:700; color: var(--brand-text); margin-bottom:12px;">
            Varaus vahvistettu!
        </h1>

        @if ($booking)
            <p style="color:#5A5A5A; font-size:15px; line-height:1.6; margin-bottom:8px;">
                Varauksesi #{{ $booking->id }} on nyt vahvistettu.
            </p>
            <p style="color:#5A5A5A; font-size:15px; line-height:1.6; margin-bottom:32px;">
                Vahvistusviesti on lähetetty osoitteeseen {{ $booking->customer->email }}.
            </p>
        @else
            <p style="color:#5A5A5A; font-size:15px; line-height:1.6; margin-bottom:32px;">
                Maksu onnistui. Vahvistusviesti lähetetään sinulle sähköpostitse hetken kuluttua.
            </p>
        @endif

        <button type="button" onclick="window.location.href='{{ route('public.booking.start') }}'"
            style="background: var(--brand-primary); color:white; border:none; padding:14px 32px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; font-family: var(--brand-body-font);">
            Takaisin etusivulle
        </button>
    </div>
</x-layouts.public>