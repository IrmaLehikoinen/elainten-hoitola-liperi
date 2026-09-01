<x-kurssit::layouts.public title="Lahjakortti">
    <style>
        @media print {
            .no-print { display: none !important; }
            .gift-card-print-area { box-shadow: none !important; margin: 0 !important; }
            @page { size: A5; margin: 0; }
        }
    </style>

    <div class="no-print" style="display:flex; gap:12px; justify-content:center; margin-bottom:24px; flex-wrap:wrap;">
        <button type="button" onclick="window.location.href='{{ route('kurssit.public.gift-card.card-pdf', $giftCard->share_token) }}'"
            class="btn-brand" style="border:none; padding:10px 18px; border-radius: var(--brand-radius, 8px); font-size:14px; font-weight:600; cursor:pointer;">
            Lataa PDF
        </button>
        <button type="button" onclick="saveGiftCardAsImage()"
            style="background:white; border:1px solid var(--brand-secondary); color: var(--brand-text); padding:10px 18px; border-radius: var(--brand-radius, 8px); font-size:14px; font-weight:600; cursor:pointer;">
            Tallenna kuvana
        </button>
        <button type="button" onclick="window.print()"
            style="background:white; border:1px solid var(--brand-secondary); color: var(--brand-text); padding:10px 18px; border-radius: var(--brand-radius, 8px); font-size:14px; font-weight:600; cursor:pointer;">
            Tulosta
        </button>
    </div>

    <div id="gift-card-print-area" class="gift-card-print-area" style="max-width:480px; margin:0 auto 32px; background:white; border-radius: var(--brand-radius, 16px); box-shadow:0 1px 2px rgba(0,0,0,0.03), 0 12px 32px -20px rgba(0,0,0,0.10); border:1px solid rgba(42,52,40,0.08); padding:44px 40px; text-align:center;">

        <p style="font-family: var(--brand-body-font); font-size:11px; letter-spacing:2px; text-transform:uppercase; color: var(--brand-primary); margin:0 0 28px;">
            @if ($giftCard->purchaser_name)
                {{ $giftCard->purchaser_name }} lähetti sinulle lahjakortin
            @else
                Sait lahjakortin
            @endif
        </p>

        <h1 style="font-family: var(--brand-heading-font); font-weight:600; font-size:24px; margin:0 0 20px; color: var(--brand-text);">
            Lahjakortti
        </h1>

        <div style="padding:24px 0; border-top:1px solid var(--brand-secondary); border-bottom:1px solid var(--brand-secondary); margin:0 0 28px;">
            <p style="font-family: var(--brand-body-font); font-size:11px; letter-spacing:1.5px; text-transform:uppercase; color: var(--brand-primary); margin:0 0 6px;">
                Arvo
            </p>
            <p style="font-family: var(--brand-heading-font); font-size:38px; color: var(--brand-primary); margin:0 0 16px;">
                {{ number_format($giftCard->initial_amount, 2, ',', ' ') }} €
            </p>
            <p style="font-family: var(--brand-body-font); font-size:13px; color: var(--brand-text); opacity:0.6; margin:0; letter-spacing:1px;">
                Koodi {{ $giftCard->code }} &nbsp;·&nbsp; Voimassa {{ $giftCard->valid_until?->format('d.m.Y') ?? 'toistaiseksi' }}
            </p>
        </div>

        <p style="font-family: var(--brand-body-font); font-size:14px; line-height:1.7; color: var(--brand-text); opacity:0.85; margin:0;">
            Käytä koodi ilmoittautuessasi kurssille.
        </p>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function saveGiftCardAsImage() {
            html2canvas(document.getElementById('gift-card-print-area'), { backgroundColor: '#ffffff', scale: 2 }).then(function (canvas) {
                const link = document.createElement('a');
                link.download = 'lahjakortti-{{ $giftCard->code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</x-kurssit::layouts.public>