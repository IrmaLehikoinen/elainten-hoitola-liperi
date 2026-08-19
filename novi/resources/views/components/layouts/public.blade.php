<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $company->name ?? 'Ajanvaraus' }} – Ajanvaraus</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-primary: {{ $company->primary_color ?? '#3F4F3A' }};
            --brand-secondary: {{ $company->secondary_color ?? '#D8C6BD' }};
            --brand-text: #2A3428;
            --brand-background: #F8F6F2;
        }

        body {
            background-color: var(--brand-background);
            color: var(--brand-text);
        }

        .btn-brand {
            background-color: var(--brand-primary);
            color: white;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold" style="color: var(--brand-text);">
                {{ $company->name ?? 'Ajanvaraus' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">Varaa hoitoaika lemmikillesi</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm sm:p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>