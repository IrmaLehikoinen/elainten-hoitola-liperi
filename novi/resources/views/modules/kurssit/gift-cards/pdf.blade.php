@php
    $company = $giftCard->company;
    $primaryColor = $company->primary_color ?? '#3F4F3A';
    $secondaryColor = $company->secondary_color ?? '#D8C6BD';
@endphp
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body { font-family: "DejaVu Sans", sans-serif; color: #2A3428; margin: 0; padding: 0; background: #F8F6F2; }
        .card { margin: 24px; padding: 40px 36px; background: white; border: 1px solid #EAE3D3; border-radius: 16px; text-align: center; }
        .eyebrow { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: {{ $primaryColor }}; margin: 0 0 20px; }
        h1 { font-family: "DejaVu Serif", serif; font-size: 22px; font-weight: bold; margin: 0 0 18px; color: #2A3428; }
        .value-box { padding: 18px 0; border-top: 1px solid {{ $secondaryColor }}; border-bottom: 1px solid {{ $secondaryColor }}; margin: 0 0 20px; }
        .value-label { font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: {{ $primaryColor }}; margin: 0 0 6px; }
        .value { font-family: "DejaVu Serif", serif; font-size: 32px; color: {{ $primaryColor }}; margin: 0 0 12px; }
        .meta { font-size: 11px; color: #8A8A7E; letter-spacing: 1px; margin: 0; }
        .footer-text { font-size: 12px; color: #4B4B44; margin: 0; }
        .company-name { font-family: "DejaVu Serif", serif; font-size: 13px; letter-spacing: 1px; color: #2A3428; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <p class="eyebrow">
            @if ($giftCard->purchaser_name)
                {{ $giftCard->purchaser_name }} lähetti sinulle lahjakortin
            @else
                Sait lahjakortin
            @endif
        </p>

        <h1>Lahjakortti</h1>

        <div class="value-box">
            <p class="value-label">Arvo</p>
            <p class="value">{{ number_format($giftCard->initial_amount, 2, ',', ' ') }} €</p>
            <p class="meta">Koodi {{ $giftCard->code }} &middot; Voimassa {{ $giftCard->valid_until?->format('d.m.Y') ?? 'toistaiseksi' }}</p>
        </div>

        <p class="footer-text">Käytä koodi ilmoittautuessasi kurssille.</p>

        <p class="company-name">{{ $company->name }}</p>
    </div>
</body>
</html>