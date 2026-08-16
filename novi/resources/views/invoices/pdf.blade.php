<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #2A3428;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #3F4F3A;
            color: white;
            padding: 24px 30px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0 0 4px;
        }

        .header p {
            margin: 0;
            font-size: 11px;
        }

        .body {
            padding: 30px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-table td {
            vertical-align: top;
            padding-right: 30px;
        }

        .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9a9188;
            margin: 0 0 3px;
        }

        .value {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items th {
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9a9188;
            border-bottom: 1px solid #D8C6BD;
            padding-bottom: 8px;
        }

        table.items td {
            padding: 8px 0;
            border-bottom: 1px solid #f0eee9;
        }

        .text-right {
            text-align: right;
        }

        .totals-table {
            width: 100%;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 4px 0;
        }

        .totals-table .muted {
            color: #9a9188;
        }

        .total-row td {
            font-weight: bold;
            font-size: 15px;
            color: #3F4F3A;
            border-top: 1px solid #D8C6BD;
            padding-top: 10px;
        }
        .footer {
            margin-top: 48px;
            padding-top: 14px;
            border-top: 1px solid #f0eee9;
            font-size: 9px;
            color: #9a9188;
        }

        .payment-box {
            margin-top: 32px;
            padding: 20px 26px;
            border: 1px solid #E9E5DE;
            border-radius: 8px;
        }

        .payment-box .section-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9a9188;
            margin: 0 0 16px;
        }

        .parties-table {
            width: 100%;
        }

        .parties-table td {
            vertical-align: top;
            width: 50%;
            padding-right: 24px;
        }

        .parties-table .value {
            font-weight: normal;
            line-height: 1.6;
        }

        .payee-name {
            font-size: 13px;
            font-weight: bold;
            color: #2A3428;
            margin: 0 0 2px;
        }

        .payee-meta {
            font-size: 9.5px;
            color: #9a9188;
            margin: 0 0 10px;
        }

        .payee-iban-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9a9188;
            margin: 0 0 2px;
        }

        .payee-iban-value {
            font-size: 12px;
            font-weight: bold;
            color: #2A3428;
            margin: 0;
        }

        .details-table {
            width: 100%;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #F1EEE8;
        }

        .details-table td {
            vertical-align: top;
            padding-right: 24px;
        }

        .amount-value {
            font-size: 15px;
            color: #3F4F3A;
        }
    </style>
       
</head>
<body>
    <div class="header">
     <h1>{{ $invoice->company->settings['official_name'] ?? ($invoice->company->name ?? 'Kuitti') }}</h1>
        <p>{{ $isInvoice ? 'Lasku' : 'Kuitti' }} @if ($invoice->booking) &middot; {{ $invoice->booking->arrival_at?->format('d.m.Y') }} &ndash; {{ $invoice->booking->pickup_at?->format('d.m.Y') }} @endif</p>
        <p>{{ $isInvoice ? 'Laskun numero' : 'Kuitin numero' }}: {{ $isInvoice ? str_replace('KUITTI', 'LASKU', $invoice->invoice_number) : $invoice->invoice_number }}</p>
    </div>

    <div class="body">
        <table class="info-table">
            <tr>
                <td>
                    <p class="label">Asiakas</p>
                    <p class="value">{{ $invoice->customer->name ?? 'Tuntematon asiakas' }}</p>
                </td>
                @if ($invoice->booking)
                    <td>
                        <p class="label">Hoitojakso</p>
                        <p class="value">{{ $invoice->booking->arrival_at?->format('d.m.Y') }} &ndash; {{ $invoice->booking->pickup_at?->format('d.m.Y') }}</p>
                    </td>
                @endif
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Erittely</th>
                    <th class="text-right">Vrk</th>
                    <th class="text-right">Á-hinta</th>
                    <th class="text-right">Yhteensä</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->line_items as $item)
                    <tr>
                        <td>{{ $item['label'] }}</td>
                        <td class="text-right">{{ $item['days'] ?? '-' }}</td>
                        <td class="text-right">{{ isset($item['rate']) ? number_format((float) $item['rate'], 2, ',', ' ') . ' EUR' : '-' }}</td>
                        <td class="text-right">{{ number_format((float) $item['subtotal'], 2, ',', ' ') }} EUR</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Veroton hinta</td>
                <td class="text-right">{{ number_format((float) $invoice->subtotal - (float) $invoice->vat_amount, 2, ',', ' ') }} EUR</td>
            </tr>
            @if ($invoice->vat_amount > 0)
                <tr>
                    <td>ALV {{ rtrim(rtrim(number_format((float) $invoice->vat_percentage, 1, ',', ' '), '0'), ',') }}%</td>
                    <td class="text-right">{{ number_format((float) $invoice->vat_amount, 2, ',', ' ') }} EUR</td>
                </tr>
            @endif
            @if ($invoice->deposit_amount > 0)
                <tr>
                    <td class="muted">Maksettu ennakkomaksu</td>
                    <td class="text-right muted">&minus; {{ number_format((float) $invoice->deposit_amount, 2, ',', ' ') }} EUR</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Maksettava</td>
                <td class="text-right">{{ number_format((float) $invoice->total_due, 2, ',', ' ') }} EUR</td>
            </tr>
        </table>

        @if ($isInvoice)
            <div class="payment-box">
                <p class="section-label">Laskun maksutiedot</p>

                <table class="parties-table">
                    <tr>
                    <td>
                            <p class="label">Saaja</p>
                            <p class="payee-name">{{ $invoice->company->settings['official_name'] ?? $invoice->company->name }}</p>
                            <p class="payee-meta">
                                @if (!empty($invoice->company->settings['address'])) {{ $invoice->company->settings['address'] }} @endif
                                @if (!empty($invoice->company->settings['address']) && !empty($invoice->company->settings['business_id'])) &middot; @endif
                                @if (!empty($invoice->company->settings['business_id'])) Y-tunnus {{ $invoice->company->settings['business_id'] }} @endif
                            </p>
                            @if (!empty($invoice->company->settings['iban']))
                                <p class="payee-iban-label">IBAN</p>
                                <p class="payee-iban-value">{{ $invoice->company->settings['iban'] }}</p>
                            @endif
                        </td>    
                        <td>
                            <p class="label">Maksaja</p>
                            <p class="value">
                                {{ $invoice->customer->name ?? '' }}<br>
                                @if (!empty($invoice->customer->address)) {{ $invoice->customer->address }} @endif
                            </p>
                        </td>
                    </tr>
                </table>

                <table class="details-table">
                    <tr>
                        <td>
                            <p class="label">Laskunumero</p>
                            <p class="value">{{ str_replace('KUITTI', 'LASKU', $invoice->invoice_number) }}</p>
                        </td>
                        <td>
                            <p class="label">Viitenumero</p>
                            <p class="value">{{ $invoice->referenceNumber() }}</p>
                        </td>
                        <td>
                            <p class="label">Eräpäivä</p>
                            <p class="value">{{ $invoice->dueDate()?->format('d.m.Y') }}</p>
                        </td>
                        <td>
                            <p class="label">Summa</p>
                            <p class="value amount-value">{{ number_format((float) $invoice->total_due, 2, ',', ' ') }} EUR</p>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <div class="footer">
            {{ $invoice->company->settings['official_name'] ?? ($invoice->company->name ?? '') }}
            @if (!empty($invoice->company->phone)) &middot; Puh. {{ $invoice->company->phone }} @endif
            @if (!empty($invoice->company->settings['business_id'])) &middot; Y-tunnus {{ $invoice->company->settings['business_id'] }} @endif
        </div>
    </div>
</body>
</html>