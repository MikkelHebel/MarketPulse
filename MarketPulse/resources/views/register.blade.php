<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h1>Register</h1>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name') <p>{{ $message }}</p> @enderror

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <p>{{ $message }}</p> @enderror

        <label>Password</label>
        <input type="password" name="password">
        @error('password') <p>{{ $message }}</p> @enderror

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation">

        <button type="submit">Register</button>
    </form>

    <a href="{{ route('login') }}">Already have an account?</a>
</body>
</html>
