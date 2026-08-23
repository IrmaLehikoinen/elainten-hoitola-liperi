@props(['step' => null, 'totalSteps' => 6, 'stepLabels' => ['Palvelu', 'Aika', 'Tunnistus', 'Tiedot', 'Varausmaksun maksaminen', 'Valmis']])

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand['name'] ?? 'Ajanvaraus' }} – Ajanvaraus</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:600,700|inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0;">
    <x-booking-widget :step="$step" :total-steps="$totalSteps" :step-labels="$stepLabels">
        {{ $slot }}

        @isset($footer)
            <x-slot:footer>
                {{ $footer }}
            </x-slot:footer>
        @endisset
    </x-booking-widget>
</body>
</html>