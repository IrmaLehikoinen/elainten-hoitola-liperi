@php
    $company = $giftCard->company;
    $primary = $company->primary_color ?? '#3F4F3A';
    $secondary = $company->secondary_color ?? '#D8C6BD';
    $headingFont = $company->settings['font_heading'] ?? 'Playfair Display';
    $bodyFont = $company->settings['font_body'] ?? 'Inter';
@endphp
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:600,700|inter:400,500,600|montserrat:600,700|open-sans:400,500,600|merriweather:600,700|lato:400,500,600|poppins:500,600,700&display=swap" rel="stylesheet">
</head>
<body style="margin:0; padding:32px 16px; background:#F8F6F2; font-family: '{{ $bodyFont }}', Helvetica, Arial, sans-serif;">
    <div style="max-width:520px; margin:0 auto; background:#FFFFFF; border:1px solid #EAE3D3; border-radius:16px; padding:44px 40px; text-align:center; color:#2A3428;">

        <p style="font-family: '{{ $bodyFont }}', Helvetica, Arial, sans-serif; font-size:11px; letter-spacing:2px; text-transform:uppercase; color:{{ $primary }}; margin:0 0 28px;">
            @if ($purchaserName)
                {{ $purchaserName }} lähetti sinulle lahjakortin
            @else
                Sait lahjakortin
            @endif
        </p>

        <h1 style="font-family: '{{ $headingFont }}', Georgia, serif; font-weight:600; font-size:24px; margin:0 0 20px; color:#2A3428;">
            Hei{{ $recipientName ? ' '.$recipientName : '' }},
        </h1>

        <p style="font-size:15px; line-height:1.7; color:#4B4B44; margin:0 0 24px; text-align:left;">
            @if ($purchaserName)
                {{ $purchaserName }} on ostanut sinulle lahjakortin {{ $company->name }}lle.
            @else
                Sait lahjakortin {{ $company->name }}lta.
            @endif
                        @if ($giftMessage)
                Hän lähetti sinulle tämän tervehdyksen:
            @endif
        </p>

        @if ($giftMessage)
            <div style="background:{{ $secondary }}; border-radius:12px; padding:20px 24px; margin:0 0 32px;">
                <p style="font-family: '{{ $headingFont }}', Georgia, serif; font-style:italic; font-size:16px; color:#2A3428; margin:0;">
                    "{{ $giftMessage }}"
                </p>
            </div>
        @endif

        <div style="padding:24px 0; border-top:1px solid #EAE3D3; border-bottom:1px solid #EAE3D3; margin:0 0 28px;">
            <p style="font-size:11px; letter-spacing:1.5px; text-transform:uppercase; color:{{ $primary }}; margin:0 0 6px;">
                Lahjakortin arvo
            </p>
            <p style="font-family: '{{ $headingFont }}', Georgia, serif; font-size:38px; color:{{ $primary }}; margin:0 0 16px;">
                {{ number_format($giftCard->initial_amount, 2, ',', ' ') }} €
            </p>
            <p style="font-size:13px; color:#8A8A7E; margin:0; letter-spacing:1px;">
                Koodi {{ $giftCard->code }} &nbsp;·&nbsp; Voimassa {{ $giftCard->valid_until->format('d.m.Y') }} asti
            </p>
        </div>

                <p style="font-size:14px; line-height:1.7; color:#4B4B44; margin:0 0 24px;">
            Voit käyttää lahjakortin ilmoittautuessasi {{ $company->name }}n kurssille.
        </p>

        <p style="margin:0 0 32px;">
            <a href="{{ route('kurssit.public.gift-card.card', $giftCard->share_token) }}" style="display:inline-block; background:{{ $primary }}; color:white; text-decoration:none; padding:12px 24px; border-radius:8px; font-size:14px; font-weight:600;">
                Näytä ja tulosta kortti
            </a>
        </p>

        @if ($company->logo_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($company->logo_path) }}" style="max-height:40px; margin-bottom:8px;" alt="{{ $company->name }}">
        @else
            <p style="font-size:13px; color:#8A8A7E; margin:0 0 4px;">Lämpimästi tervetuloa</p>
            <p style="font-family: '{{ $headingFont }}', Georgia, serif; font-size:15px; letter-spacing:1px; color:#2A3428; margin:0;">
                {{ $company->name }}
            </p>
        @endif
    </div>
</body>
</html>