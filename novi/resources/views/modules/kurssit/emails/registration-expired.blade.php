@include('partials.emails.header')

<h1 style="font-size: 20px;">Ilmoittautumisesi on peruuntunut</h1>

<p>Hei {{ $registration->name }},</p>

<p>Kurssimaksua ei suoritettu määräaikaan mennessä, joten ilmoittautumisesi kurssille {{ $registration->course->name }} on peruuntunut ja paikka on vapautunut muille.</p>

<p style="margin-top: 24px;">Jos haluat vielä osallistua ja paikkoja on vapaana, voit ilmoittautua uudelleen.</p>

@include('partials.emails.footer')