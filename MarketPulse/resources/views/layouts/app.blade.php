<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPulse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @auth
        @vite(['resources/js/notifications.js', 'resources/js/search.js'])
    @endauth
</head>
<body class="bg-white text-gray-900 min-h-screen">
    {{-- Nav --}}
    <nav class="bg-orange-500 text-white px-6 py-4 flex items-center justify-between shadow">
        <a href="{{ route('dashboard') }}" class="text-2xl font-bold tracking-tight">MarketPulse</a>

        <form method="GET" action="{{ route('search') }}">
            @auth
                <input type="text" name="ticker" placeholder="Search a ticker e.g. ASTS or ASML" class="w-80 bg-white text-gray-800 placeholder-gray-400 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200">
            @endauth
            @guest
                <input type="text" placeholder="Search a ticker e.g. ASTS or ASML" disabled title="Only logged in users can use search" class="w-80 bg-white text-gray-800 placeholder-gray-400 rounded-lg px-4 py-2 text-sm opacity-60 cursor-not-allowed">
            @endguest
        </form>

        <div class="flex items-center gap-6 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="hover:text-orange-100">Dashboard</a>
            @auth
                <a href="{{ route('thresholds') }}" class="hover:text-orange-100">Alerts</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:text-orange-100 cursor-pointer">Logout</button>
                </form>
            @endauth
            @guest
                <a href="{{ route('login') }}" class="hover:text-orange-100">Login</a>
                <a href="{{ route('register') }}" class="hover:text-orange-100">Register</a>
            @endguest
        </div>
    </nav>

    {{-- Notfication --}}
    <div id="notification-container" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 items-center"></div>

    {{-- Page content --}}
    <main class="max-w-7xl mx-auto px-6 py-10">
        @yield('content')
    </main>
</body>
</html>
