@include('partials.emails.header')

<h1 style="font-size: 20px;">Ilmoittautumisesi odottaa maksua</h1>

<p>Hei {{ $registration->name }},</p>

<p>Ilmoittautumisesi on vastaanotettu. Vahvistaaksesi paikkasi, maksa kurssimaksu alla olevasta linkistä.</p>

<table style="margin-top: 16px;">
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Kurssi:</td>
        <td>{{ $registration->course->name }}</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Ajankohta:</td>
        <td>{{ $registration->course->starts_at?->format('d.m.Y H:i') }}</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Hinta:</td>
        <td>{{ number_format((float) $registration->course->price, 2, ',', ' ') }} €</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Maksettava viimeistään:</td>
        <td>{{ $registration->payment_deadline?->format('d.m.Y H:i') }}</td>
    </tr>
</table>

<p style="margin-top: 24px;">
    <a href="{{ $paymentUrl }}" style="background-color: #4F46E5; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
        Maksa kurssimaksu
    </a>
</p>

<p style="margin-top: 24px; color: #6b7280; font-size: 13px;">
    Jos maksua ei suoriteta määräaikaan mennessä, paikka vapautuu automaattisesti.
</p>

@include('partials.emails.footer')