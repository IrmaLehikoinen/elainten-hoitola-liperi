<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <title>Tietopyyntö — {{ $customer->name }}</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; color: #2A3428; max-width: 720px; margin: 0 auto; padding: 32px 24px 80px; line-height: 1.5; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        h2 { font-size: 16px; margin-top: 28px; margin-bottom: 6px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        p { font-size: 14px; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td, th { text-align: left; padding: 4px 8px 4px 0; font-size: 13px; vertical-align: top; }
        .pet-block { border: 1px solid #eee; border-radius: 6px; padding: 12px; margin-top: 10px; }
        .no-print { margin-top: 24px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <h1>Tietopyyntö: {{ $customer->name }}</h1>
    <p style="color:#777;">Koottu {{ now()->format('d.m.Y H:i') }}</p>

    <h2>Yhteystiedot</h2>
    <p>Nimi: {{ $customer->name }}</p>
    <p>Puhelin: {{ $customer->phone ?? '—' }}</p>
    <p>Sähköposti: {{ $customer->email ?? '—' }}</p>
    <p>Osoite: {{ $customer->address ?? '—' }}</p>
    <p>Muistiinpanot: {{ $customer->notes ?? '—' }}</p>

    <h2>Lemmikit ({{ $customer->pets->count() }})</h2>
    @forelse ($customer->pets as $pet)
        <div class="pet-block">
            <p><strong>{{ $pet->name }}</strong> — {{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
            <p>Syntymäaika: {{ $pet->birth_date?->format('d.m.Y') ?? '—' }} · Sukupuoli: {{ $pet->sex ?? '—' }} · Paino: {{ $pet->weight ?? '—' }}</p>
            <p>Mikrosiru: {{ $pet->microchip_number ?? '—' }}</p>
            <p>Rokotukset: {{ $pet->vaccinations ?? '—' }}</p>
            <p>Allergiat: {{ $pet->allergies ?? '—' }}</p>
            <p>Lääkitykset: {{ $pet->medications ?? '—' }}</p>
            <p>Ruokintaohjeet: {{ $pet->feeding_instructions ?? '—' }}</p>
            <p>Käytöstiedot: {{ $pet->behaviour_notes ?? '—' }}</p>
            <p>Eläinlääkäri: {{ $pet->veterinarian_name ?? '—' }} {{ $pet->veterinarian_phone ?? '' }}</p>
            <p>Hätätilanneohjeet: {{ $pet->emergency_notes ?? '—' }}</p>
            <p>Asiakkaan tiedot: {{ $pet->general_notes ?? '—' }}</p>
            @if ($includeInternal ?? false)
                <p>Hoitolan sisäiset muistiinpanot: {{ $pet->internal_notes ?? '—' }}</p>
            @endif
        </div>
    @empty
        <p>Ei lemmikkejä.</p>
    @endforelse

    <h2>Varaukset ({{ $customer->bookings->count() }})</h2>
    <table>
        <tr><th>Ajankohta</th><th>Tila</th><th>Summa</th><th>Ennakkomaksu</th></tr>
        @forelse ($customer->bookings as $booking)
            <tr>
                <td>{{ $booking->start_date?->format('d.m.Y') }} – {{ $booking->end_date?->format('d.m.Y') }}</td>
                <td>{{ $booking->status }}</td>
                <td>{{ number_format((float) $booking->total_price, 2, ',', ' ') }} €</td>
                <td>{{ number_format((float) $booking->deposit_amount, 2, ',', ' ') }} € {{ $booking->deposit_paid_at ? '(maksettu)' : '' }}</td>
            </tr>
        @empty
            <tr><td colspan="4">Ei varauksia.</td></tr>
        @endforelse
    </table>

    <h2>Kuitit/laskut ({{ $customer->invoices->count() }})</h2>
    <table>
        <tr><th>Numero</th><th>Päivä</th><th>Summa</th></tr>
        @forelse ($customer->invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->issued_at?->format('d.m.Y') }}</td>
                <td>{{ number_format((float) $invoice->total_due, 2, ',', ' ') }} €</td>
            </tr>
        @empty
            <tr><td colspan="3">Ei kuitteja.</td></tr>
        @endforelse
    </table>

    @unless (isset($isPdf))
        <div class="no-print">
            <button onclick="window.print()">Tulosta</button>
            <button onclick="window.location.href='{{ route('admin.customers.data-export.pdf', $customer) }}{{ ($includeInternal ?? false) ? '?include_internal=1' : '' }}'">Lataa PDF</button>
        </div>
    @endunless       
</body>
</html>