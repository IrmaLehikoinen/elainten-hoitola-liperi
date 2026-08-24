@include('partials.emails.header')

<h1 style="font-size: 20px;">Varausmaksu odottaa maksua</h1>

<p>Hei {{ $booking->customer->name ?? '' }},</p>

<p>Varauksesi on tallennettu. Vahvistaaksesi varauksen, maksa ennakkomaksu alla olevasta linkistä.</p>

<table style="margin-top: 16px;">
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Saapuminen:</td>
        <td>{{ $booking->arrival_at?->format('d.m.Y H:i') }}</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Ennakkomaksu:</td>
        <td>{{ number_format((float) $booking->deposit_amount, 2, ',', ' ') }} €</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Maksettava viimeistään:</td>
        <td>{{ $booking->payment_deadline?->format('d.m.Y H:i') }}</td>
    </tr>
</table>

<p style="margin-top: 24px;">
    <a href="{{ $paymentUrl }}" style="background-color: #4F46E5; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
        Maksa varausmaksu
    </a>
</p>

<p style="margin-top: 24px; color: #6b7280; font-size: 13px;">
    Jos maksua ei suoriteta määräaikaan mennessä, varaus peruuntuu automaattisesti.
</p>

@include('partials.emails.footer')