@include('partials.emails.header')

<h1 style="font-size: 20px;">Kurssi on peruttu</h1>

<p>Hei {{ $registration->name }},</p>

<p>Valitettavasti kurssi {{ $registration->course->name }} on peruttu.</p>

@if ($registration->course->starts_at)
<p>
    Alun perin ajankohta oli: {{ $registration->course->starts_at->format('d.m.Y H:i') }}
</p>
@endif

@if ($registration->paid_at)
<p style="margin-top: 24px;">Olet ehtinyt maksaa kurssin — otamme sinuun yhteyttä maksun palautuksesta.</p>
@endif

<p style="margin-top: 24px;">Pahoittelemme aiheutunutta haittaa.</p>

@include('partials.emails.footer')