@props(['title' => null])

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($brand['name'] ?? 'Kurssit') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:600,700|inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0; background: var(--brand-background, #F8F6F2); font-family: var(--brand-body-font, sans-serif);">
    <div style="max-width: 640px; margin: 0 auto; padding: 48px 24px;">
        {{ $slot }}
    </div>
</body>
</html>