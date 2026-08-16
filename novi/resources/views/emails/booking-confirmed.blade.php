<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #111827; padding: 24px;">
    <h1 style="font-size: 20px;">Varauksesi on vahvistettu</h1>

    <p>Hei {{ $booking->customer->name ?? '' }},</p>

    <p>Ennakkomaksusi on vastaanotettu ja varauksesi on nyt vahvistettu.</p>

    <table style="margin-top: 16px;">
        <tr>
            <td style="padding-right: 12px; color: #6b7280;">Saapuminen:</td>
            <td>{{ $booking->arrival_at?->format('d.m.Y H:i') }}</td>
        </tr>
        <tr>
            <td style="padding-right: 12px; color: #6b7280;">Nouto:</td>
            <td>{{ $booking->pickup_at?->format('d.m.Y H:i') }}</td>
        </tr>
    </table>

    <p style="margin-top: 24px;">Nähdään pian!</p>
</body>
</html>