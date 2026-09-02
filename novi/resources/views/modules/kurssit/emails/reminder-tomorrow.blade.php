@include('partials.emails.header')

<h1 style="font-size: 20px;">Muistutus huomisesta {{ $registration->course->company->name }}-ryhmästä</h1>

<p>Hei {{ $registration->name }},</p>

<p>Muistutuksena, että ilmoittautumasi {{ $registration->course->name }} on huomenna.</p>

<p>
    Ryhmä: {{ $registration->course->name }}<br>
    Ajankohta: {{ $registration->course->starts_at?->format('d.m.Y H:i') }}
</p>

<p style="margin-top: 24px;">Nähdään huomenna! 💛</p>

@include('partials.emails.footer')