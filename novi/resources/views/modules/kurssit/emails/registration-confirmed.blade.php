@include('partials.emails.header')

<h1 style="font-size: 20px;">Ilmoittautumisesi on vahvistettu</h1>

<p>Hei {{ $registration->name }},</p>

<p>Ilmoittautumisesi kurssille on vahvistettu.</p>

<table style="margin-top: 16px;">
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Kurssi:</td>
        <td>{{ $registration->course->name }}</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Ajankohta:</td>
        <td>{{ $registration->course->starts_at?->format('d.m.Y H:i') }}</td>
    </tr>
</table>

<p style="margin-top: 24px;">Nähdään pian!</p>

@include('partials.emails.footer')