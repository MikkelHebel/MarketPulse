<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MarketPulse</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <a href="{{ route('dashboard') }}" class="text-3xl font-bold text-orange-500 tracking-tight">MarketPulse</a>
            <p class="text-sm text-gray-400 mt-1">Sign in to your account</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            @if(session('error') || $errors->has('credentials'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
                    {{ session('error') ?? $errors->first('credentials') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
                @csrf

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('email') border-red-300 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" required
                        class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>

                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2.5 rounded-lg text-sm transition-colors cursor-pointer mt-1">
                    Sign in
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-600 font-medium">Register</a>
        </p>
    </div>

</body>
</html>
