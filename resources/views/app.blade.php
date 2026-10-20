<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>GlobalTECH EDUCATION Africa</title>

        {{-- Favicon and social sharing images --}}
        @php
            $siteName = 'GlobalTECH EDUCATION Africa';
            $siteDescription = 'Leader en formations certifiantes PMP, Informatique et Management en Afrique.';
            $shareImage = asset('images/logo_globaltech.jpeg');
        @endphp
        <meta name="description" content="{{ $siteDescription }}" />
        <link rel="icon" type="image/jpeg" href="{{ $shareImage }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ $shareImage }}">
        <link rel="apple-touch-icon" href="{{ $shareImage }}">

        {{-- Rendered server-side: social crawlers (WhatsApp, Facebook, LinkedIn…) don't execute JS --}}
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="{{ $siteName }}" />
        <meta property="og:title" content="{{ $siteName }}" />
        <meta property="og:description" content="{{ $siteDescription }}" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:locale" content="fr_FR" />
        <meta property="og:image" content="{{ $shareImage }}" />
        <meta property="og:image:alt" content="{{ $siteName }}" />
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="{{ $siteName }}" />
        <meta name="twitter:description" content="{{ $siteDescription }}" />
        <meta name="twitter:image" content="{{ $shareImage }}" />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
