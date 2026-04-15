<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Exicompras') }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('images/exicompras.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased" style="font-family: 'Inter', sans-serif;">
        <div class="min-h-screen flex">
            {{-- Panel izquierdo: Branding --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-40 -left-40 w-80 h-80 bg-amber-500 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-20 right-10 w-96 h-96 bg-amber-400 rounded-full blur-3xl"></div>
                </div>
                <div class="relative z-10 flex flex-col items-center justify-center w-full px-12">
                    <img src="{{ asset('images/exicompras.jpg') }}" alt="Exicompras" class="w-28 h-28 rounded-2xl shadow-2xl mb-8 object-cover ring-4 ring-white/10">
                    <h1 class="text-4xl font-bold text-white tracking-tight mb-3">Exicompras</h1>
                    <p class="text-slate-400 text-center text-lg max-w-sm leading-relaxed">Tu plataforma de compras en línea.</p>
                </div>
            </div>

            {{-- Panel derecho: Formulario --}}
            <div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-6 sm:px-12 bg-gray-50">
                {{-- Logo visible solo en móvil --}}
                <div class="lg:hidden flex flex-col items-center mb-8">
                    <img src="{{ asset('images/exicompras.jpg') }}" alt="Exicompras" class="w-20 h-20 rounded-xl shadow-lg object-cover mb-4">
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Exicompras</h1>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
