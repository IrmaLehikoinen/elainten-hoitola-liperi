@include('partials.emails.header')

<h1 style="font-size: 20px;">Varauksesi on peruuntunut</h1>

<p>Hei {{ $booking->customer->name ?? '' }},</p>

<p>Varauksesi ajalle {{ $booking->arrival_at?->format('d.m.Y') }} – {{ $booking->pickup_at?->format('d.m.Y') }} on peruuntunut, koska varausmaksua ei maksettu määräaikaan mennessä.</p>

<p style="margin-top: 24px; color: #6b7280; font-size: 13px;">
    Jos haluat varata ajan uudelleen, voit tehdä sen verkkosivuiltamme.
</p>

@include('partials.emails.footer')