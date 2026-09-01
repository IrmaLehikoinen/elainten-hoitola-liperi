@props(['title' => null])

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($brand['name'] ?? 'Kurssit') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:600,700|inter:400,500,600|montserrat:600,700|open-sans:400,500,600|merriweather:600,700|lato:400,500,600|poppins:500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .kurssit-public {
            --brand-primary: {{ $brand['primary_color'] ?? '#3F4F3A' }};
            --brand-secondary: {{ $brand['secondary_color'] ?? '#D8C6BD' }};
            --brand-accent: {{ $brand['accent_color'] ?? '#2A3428' }};
            --brand-background: {{ $brand['background_color'] ?? '#F8F6F2' }};
            --brand-text: {{ $brand['text_color'] ?? '#2A3428' }};
            --brand-heading-font: '{{ $brand['font_heading'] ?? 'Playfair Display' }}', serif;
            --brand-body-font: '{{ $brand['font_body'] ?? 'Inter' }}', sans-serif;
            --brand-radius: {{ $brand['border_radius'] ?? '16px' }};

            margin: 0;
            font-family: var(--brand-body-font);
            color: var(--brand-text);
            background-color: var(--brand-background);
        }
    </style>
</head>
<body class="kurssit-public">
         <div style="max-width: 960px; margin: 0 auto; padding: 48px 24px;">   
        {{ $slot }}
    </div>
</body>
</html>