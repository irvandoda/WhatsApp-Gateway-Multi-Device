<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    {{-- 
        Copyright By irvandoda.my.id
        You are not allowed to share or sell this source code without permission.
    --}}
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
        <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                neon: '#5EF1EE',
                                deep: '#0F172A',
                                halo: '#1d2b53'
                            }
                        },
                        boxShadow: {
                            glow: '0 0 50px rgba(94, 241, 238, 0.25)'
                        }
                    }
                }
            }
        </script>
        <style>
            [x-cloak] {
                display: none !important;
            }

            body {
                font-family: 'Space Grotesk', sans-serif;
            }
        </style>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <title>{{ $title }} | irvandoda WhatsApp Command Center</title>
    </head>

    <body class="bg-slate-950 text-slate-100 antialiased">
        <div x-data="{ mobileSidebar: false }" class="min-h-screen flex">
            <div
                class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-900/90 border-r border-slate-800/50 backdrop-blur-xl transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
                :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
                <x-aside />
            </div>

            <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
                <x-header />
                <main class="flex-1 px-4 py-8 lg:px-10">
                    {{ $slot }}
                </main>
            </div>

            <div x-cloak x-show="mobileSidebar" class="fixed inset-0 z-30 bg-slate-950/70 backdrop-blur lg:hidden"
                @click="mobileSidebar = false"></div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script>
            toastr.options = {
                closeButton: false,
                debug: false,
                newestOnTop: false,
                progressBar: false,
                positionClass: "toast-top-right",
                preventDuplicates: false,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                timeOut: "4000",
                extendedTimeOut: "1000",
                showEasing: "cubic-bezier(.4,0,.2,1)",
                hideEasing: "cubic-bezier(.4,0,.2,1)",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
            };
        </script>
    </body>
</html>
