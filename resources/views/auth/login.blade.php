@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

    <div class="w-full max-w-md bg-white shadow-md rounded-lg p-8">

        <!-- LOGO -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('logo.png') }}"
                 alt="Polysync Logo"
                 class="h-14">
        </div>

        <h2 class="text-center text-2xl font-semibold mb-6">Login to Polysync</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block font-medium mb-1">Email Address</label>
                <input id="email" type="email"
                       class="w-full border px-3 py-2 rounded focus:ring focus:ring-blue-300"
                       name="email"
                       required autofocus>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block font-medium mb-1">Password</label>
                <input id="password" type="password"
                       class="w-full border px-3 py-2 rounded focus:ring focus:ring-blue-300"
                       name="password"
                       required>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center mb-4">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember">Remember Me</label>
            </div>

            <!-- Login Button -->
            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
                Login
            </button>

            <!-- Forgot Password -->
            <div class="mt-4 text-center">
                @if (Route::has('password.request'))
                    <a class="text-blue-600 hover:underline" href="{{ route('password.request') }}">
                        Forgot Your Password?
                    </a>
                @endif
            </div>
        </form>
    </div>

</div>
@endsection
