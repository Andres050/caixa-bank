<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container {
                border: 1px solid rgb(55 65 81 / var(--tw-border-opacity, 1)) !important;
                border-radius: .375rem !important;
            }

            .select2-selection__rendered {
                --tw-bg-opacity: 1;

                background-color: #1c1d20 !important;
                color: rgb(209 213 219 / var(--tw-text-opacity, 1)) !important;

                --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / .05) !important;
                --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color) !important;
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow) !important;
            }
            .select2-container--default .select2-selection--single {
                --tw-bg-opacity: 1;

                background-color: #1c1d20 !important;
                color: rgb(209 213 219 / var(--tw-text-opacity, 1)) !important;
                border: none !important;
                padding: .5rem .75rem !important;
                height: 100% !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                top: 8px !important;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-[#111214]">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-[#1c1d20] shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Select2
                $('.select2').select2({
                    placeholder: '-- Select an option --',
                    allowClear: true
                });
            });
        </script>
    </body>
</html>
