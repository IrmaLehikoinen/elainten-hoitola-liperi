@include('partials.emails.header')

<h1 style="font-size: 20px;">Ryhmä saattaa perua vähäisen osallistujamäärän vuoksi</h1>

<p>Hei {{ $appointment->name }},</p>

<p>Olet ilmoittautunut tähän ryhmään:</p>

<table style="margin-top: 16px;">
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Hoito:</td>
        <td>{{ $appointment->treatment->name }}</td>
    </tr>
    <tr>
        <td style="padding-right: 12px; color: #6b7280;">Ajankohta:</td>
        <td>{{ $appointment->starts_at->format('d.m.Y H:i') }}</td>
    </tr>
</table>

<p style="margin-top: 24px;">
    Tällä hetkellä ilmoittautuneita on {{ $currentCount }}, ja ryhmä tarvitsee vähintään {{ $minParticipants }} osallistujaa toteutuakseen.
    Jos osallistujia ei ole tarpeeksi ajankohtaan mennessä, ryhmä saatetaan joutua perumaan. Otathan yhteyttä jos sinulla on kysyttävää.
</p>

@include('partials.emails.footer')