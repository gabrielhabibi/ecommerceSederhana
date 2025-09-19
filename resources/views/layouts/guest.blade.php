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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-white">

    <!-- 🔹 Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- Logo / App Name -->
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600">
                        {{ config('app.name', 'Shopora') }}
                    </a>
                </div>

                <!-- Language Dropdown -->
                <div class="relative">
                    <button id="langDropdownBtn" class="px-4 py-2 border rounded-md bg-white text-gray-700 flex items-center">
                        {{ __('auth.language') }} <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="langDropdownMenu" class="absolute right-0 mt-2 w-40 bg-white border rounded-md shadow-lg hidden">
                        <a href="{{ url('greeting/en') }}" class="block px-4 py-2 hover:bg-gray-100">English</a>
                        <a href="{{ url('greeting/id') }}" class="block px-4 py-2 hover:bg-gray-100">Indonesia</a>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen flex flex-col items-center justify-center">
        {{ $slot }}
    </main>

    <!-- Script buat toggle dropdown -->
    <script>
        const btn = document.getElementById('langDropdownBtn');
        const menu = document.getElementById('langDropdownMenu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Klik di luar dropdown → close
        window.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
