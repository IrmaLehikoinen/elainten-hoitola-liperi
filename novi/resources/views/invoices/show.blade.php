<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuitti {{ $invoice->invoice_number }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:600,700|inter:400,500,600&display=swap" rel="stylesheet" />

    <style>
      :root {
            --brand-primary: #3F4F3A;
            --brand-secondary: #D8C6BD;
            --brand-text: #2A3428;
            --brand-background: #F8F6F2;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--brand-text);
            background-color: var(--brand-background);
            margin: 0;
            padding: 3rem 1rem;
        }

        .receipt {
            max-width: 640px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(42, 52, 40, 0.08);
        }

        .receipt-header {
            background-color: var(--brand-primary);
            color: white;
            padding: 2.75rem 2rem 2.25rem;
        }

        .receipt-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            margin: 0 0 0.35rem;
        }

        .receipt-header p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.85;
        }

        .receipt-body {
            padding: 2rem;
        }

        .info-grid {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9a9188;
            margin: 0 0 0.25rem;
        }

        .info-value {
            margin: 0;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        th {
            text-align: left;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9a9188;
            border-bottom: 1px solid var(--brand-secondary);
            padding-bottom: 0.6rem;
        }

        th.text-right, td.text-right {
            text-align: right;
        }

        td {
            padding: 0.65rem 0;
            border-bottom: 1px solid #f0eee9;
        }

        .totals {
            margin-top: 1.25rem;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            font-size: 0.9rem;
        }

        .totals-row.deposit {
            color: #9a9188;
        }

        .totals-row.total {
            margin-top: 0.75rem;
            padding: 1rem 1.25rem;
            background-color: var(--brand-background);
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--brand-primary);
        }

        .actions {
            margin-top: 2rem;
            display: flex;
            gap: 0.6rem;
        }

        button {
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.65rem 1.3rem;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid var(--brand-secondary);
            background: white;
            color: var(--brand-text);
        }

        button.primary {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
            color: white;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
         <h1>{{ $invoice->company->name ?? 'Kuitti' }}</h1>
            <p>Kuitti @if ($invoice->booking) · {{ $invoice->booking->arrival_at?->format('d.m.Y') }} – {{ $invoice->booking->pickup_at?->format('d.m.Y') }} @endif</p>
            <p style="margin-top: 0.35rem;">Kuitin numero: {{ $invoice->invoice_number }}</p>
        </div>

        <div class="receipt-body">
            <div class="info-grid">
                <div>
                    <p class="info-label">Asiakas</p>
                    <p class="info-value">{{ $invoice->customer->name ?? 'Tuntematon asiakas' }}</p>
                </div>

                @if ($invoice->booking)
                    <div>
                        <p class="info-label">Hoitojakso</p>
                        <p class="info-value">
                            {{ $invoice->booking->arrival_at?->format('d.m.Y') }} – {{ $invoice->booking->pickup_at?->format('d.m.Y') }}
                        </p>
                    </div>
                @endif
            </div>

            <table>
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
                            <td class="text-right">{{ $item['days'] ?? '—' }}</td>
                            <td class="text-right">{{ isset($item['rate']) ? number_format((float) $item['rate'], 2, ',', ' ') . ' €' : '—' }}</td>
                            <td class="text-right">{{ number_format((float) $item['subtotal'], 2, ',', ' ') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="totals-row">
                    <span>Veroton hinta</span>
                    <span>{{ number_format((float) $invoice->subtotal - (float) $invoice->vat_amount, 2, ',', ' ') }} €</span>
                </div>

                @if ($invoice->vat_amount > 0)
                    <div class="totals-row">
                        <span>ALV {{ rtrim(rtrim(number_format((float) $invoice->vat_percentage, 1, ',', ' '), '0'), ',') }}%</span>
                        <span>{{ number_format((float) $invoice->vat_amount, 2, ',', ' ') }} €</span>
                    </div>
                @endif

                @if ($invoice->deposit_amount > 0)
                    <div class="totals-row deposit">
                        <span>Maksettu ennakkomaksu</span>
                        <span>− {{ number_format((float) $invoice->deposit_amount, 2, ',', ' ') }} €</span>
                    </div>
                @endif

                <div class="totals-row total">
                    <span>Maksettava</span>
                    <span>{{ number_format((float) $invoice->total_due, 2, ',', ' ') }} €</span>
                </div>
            </div>

        <div class="actions">
                <button type="button" class="primary" onclick="window.location.href='{{ route('invoices.pdf', $invoice) }}'">Lataa kuitti</button>
                <button type="button" onclick="window.location.href='{{ route('invoices.pdf', $invoice) }}?type=lasku'">Lataa lasku</button>
                <button type="button" onclick="window.print()">Tulosta kuitti</button>
            </div>
        </div>
    </div>
</body>
</html>           