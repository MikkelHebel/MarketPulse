<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPulse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @auth
        @vite(['resources/js/notifications.js'])
    @endauth
</head>
<body class="bg-white text-gray-900 min-h-screen">
    {{-- Nav --}}
    <nav class="bg-orange-500 text-white px-6 py-4 flex items-center justify-between shadow">
        <a href="/dashboard" class="text-2xl font-bold tracking-tight">MarketPulse</a>
        <div class="flex items-center gap-6 text-sm font-medium">
            <a href="/dashboard" class="hover:text-orange-100">Dashboard</a>
            <a href="/search" class="hover:text-orange-100">Search</a>
            @auth
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
    <div id="notification-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2"></div>

    {{-- Page content --}}
    <main class="max-w-7xl mx-auto px-6 py-10">
        @yield('content')
    </main>
</body>
</html>
