<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

               <style>
            /* Novi-hallintapaneelin OMAT, kiinteät värit — eivät koskaan riipu asiakkaan verkkosivun brändistä. */
    :root {
        --brand-primary: #3F4F3A;
        --brand-secondary: #D8C6BD;
        --brand-accent: #2A3428;
        --brand-background: #F8F6F2;
        --brand-text: #2A3428;
        --brand-heading-font: 'Playfair Display', serif;
        --brand-body-font: 'Inter', sans-serif;
    }

        body {
        font-family: var(--brand-body-font);
        color: var(--brand-text);
        background-color: var(--brand-background);
    }

    header h1 {
        color: var(--brand-primary) !important;
    }
</style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 sm:pl-64">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
                 @stack('scripts')
    </body>
</html> 
