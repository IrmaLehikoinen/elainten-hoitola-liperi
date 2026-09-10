@include('partials.emails.header')

<h1 style="font-size: 20px;">Varauksesi odottaa maksua</h1>

<p>Hei {{ $appointment->name }},</p>

<p>Varauksesi on vastaanotettu. Vahvistaaksesi ajan, maksa alla olevasta linkistä.</p>

<table style="margin-top: 16px;">
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Palvelu:</td>
        <td>{{ $appointment->treatment->name }}</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Ajankohta:</td>
        <td>{{ $appointment->starts_at->format('d.m.Y H:i') }}</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Hinta:</td>
        <td>{{ number_format((float) $appointment->price, 2, ',', ' ') }} €</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Maksettava viimeistään:</td>
        <td>{{ $appointment->payment_deadline?->format('d.m.Y H:i') }}</td>
    </tr>
</table>

<p style="margin-top: 24px;">
    <a href="{{ $paymentUrl }}" style="background-color: #4F46E5; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
        Maksa varaus
    </a>
</p>

<p style="margin-top: 24px; color: #6b7280; font-size: 13px;">
    Jos maksua ei suoriteta määräaikaan mennessä, aika vapautuu automaattisesti.
</p>

@include('partials.emails.footer')