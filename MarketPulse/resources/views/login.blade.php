<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h1>Login</h1>

    @if(session('error'))
    <p>{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <p>{{ $message }}</p> @enderror

        <label>Password</label>
        <input type="password" name="password">

        <button type="submit">Login</button>
    </form>

    <a href="{{ route('register') }}">Don't have an account?</a>
</body>
</html>
