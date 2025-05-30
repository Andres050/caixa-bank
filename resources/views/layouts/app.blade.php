<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head dir="{{ Route::currentRouteName() }}" next-translation="{{ __('Next') }}" close-translation="{{ __('Close') }}">
        <meta charset="utf-8">
        <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="locale" content="{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/fonts.css'])

        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/app.css'])

        @vite(['resources/css/shepherd.css'])

        @vite(['resources/css/select2.css'])
        @vite(['resources/css/datatable.css'])

        <style>
            /** Custom styles for the application */
            @if (auth()->user()->themeMain3)
                :root {
                    --color-main3: {{ auth()->user()->themeMain3 }} !important;
                }
            @endif

            @if (auth()->user()->themeNavActive)
                :root {
                    --color-navActive: {{ auth()->user()->themeNavActive }} !important;
                }
            @endif

            @if (auth()->user()->themeNavActiveBg)
                :root {
                    --color-navActiveBg: {{ auth()->user()->themeNavActiveBg }} !important;
                }
            @endif
        </style>
    </head>
    <body id="default-step" shepherd-text="{{trans('shepherd.default')}}" class="font-Inter antialiased">
        <div class="min-h-screen bg-main1">
            <x-site.flash-messages />
            <x-site.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-main2 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="relative">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
