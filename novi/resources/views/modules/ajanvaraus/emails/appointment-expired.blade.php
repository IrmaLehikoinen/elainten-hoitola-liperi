@include('partials.emails.header')

<h1 style="font-size: 20px;">Varauksesi on peruuntunut</h1>

<p>Hei {{ $appointment->name }},</p>

<p>Maksua ei suoritettu määräaikaan mennessä, joten varauksesi ({{ $appointment->treatment->name }}, {{ $appointment->starts_at->format('d.m.Y H:i') }}) on peruuntunut ja aika on vapautunut muille.</p>

<p style="margin-top: 24px;">Jos haluat vielä varata ajan, voit tehdä uuden varauksen.</p>

@include('partials.emails.footer')