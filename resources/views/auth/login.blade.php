@extends('layouts.guest')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center relative overflow-hidden"
     style="font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', Helvetica, Arial, sans-serif;">

    {{-- BACKGROUND --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#AFC8FF] via-[#CBB9FF] to-[#A8E0FF] opacity-[0.60] animate-fadeIn"></div>

    {{-- FLOATING SPHERES --}}
    <div class="absolute w-[700px] h-[700px] bg-[#7A68FF]/40 rounded-full blur-[150px] -top-40 -left-32"></div>
    <div class="absolute w-[620px] h-[620px] bg-[#00C2FF]/35 rounded-full blur-[150px] top-32 -right-40"></div>

    {{-- HIGHLIGHT --}}
    <div class="absolute inset-0 bg-white/20 mix-blend-screen"></div>

    {{-- MAIN CARD --}}
    <div class="relative w-[1200px] max-w-[92%] rounded-[32px]
                backdrop-blur-2xl bg-white/25 border border-white/40
                shadow-[0_12px_50px_rgba(0,0,0,0.22)] overflow-hidden flex
                animate-slideUp">

        {{-- LEFT PANEL --}}
        <div class="w-1/2 relative flex flex-col items-center justify-center text-center px-10 py-16 bg-white/35">

            {{-- LOGO (NO MORE FLOATING, ONLY FADE-IN) --}}
            <img src="{{ asset('logo.png') }}"
                 class="w-[380px] opacity-95 mb-3 select-none animate-logoFade">

            <h1 class="text-[30px] font-semibold text-[#1F1F1F]/90 tracking-tight mb-1">
                Powered by Polycool
            </h1>
            <p class="text-[17px] text-[#2F2F2F]/75">
                Smart. Fast. Modern Foam Solutions.
            </p>
        </div>

        {{-- DIVIDER --}}
        <div class="w-[1px] bg-white/30 backdrop-blur-sm"></div>

        {{-- RIGHT PANEL --}}
        <div class="w-1/2 p-14 flex flex-col justify-center bg-white/20">

            <h2 class="text-center text-[28px] font-semibold text-gray-900 tracking-tight mb-1">
                Welcome Back
            </h2>
            <p class="text-center text-gray-600 text-[15px] mb-8">
                Login to your Polysync account
            </p>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Email Address</label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-3 rounded-xl transition-all duration-200
                                  bg-gray-200/40 text-gray-900 placeholder-gray-500
                                  border border-gray-300/50 backdrop-blur-sm
                                  focus:ring-2 focus:ring-[#4F9CF9] focus:outline-none
                                  hover:shadow-md hover:scale-[1.01]">
                </div>

                {{-- PASSWORD + SHOW/HIDE --}}
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Password</label>

                    <div class="relative">
                        <input type="password" name="password" id="passwordField" required
                               class="w-full px-4 py-3 rounded-xl transition-all duration-200
                                      bg-gray-200/40 text-gray-900 placeholder-gray-500
                                      border border-gray-300/50 backdrop-blur-sm
                                      focus:ring-2 focus:ring-[#4F9CF9] focus:outline-none
                                      hover:shadow-md hover:scale-[1.01]">

                        {{-- EYE ICON BUTTON --}}
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-3 text-gray-500 hover:text-gray-700 transition">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" viewBox="0 0 24 24" stroke-width="1.8" 
                                 stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                      d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.637 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.637 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- OPTIONS --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center space-x-2 text-gray-700">
                        <input type="checkbox" class="rounded border-gray-400 bg-white/30">
                        <span>Remember Me</span>
                    </label>

                    <a href="{{ route('password.request') }}" class="text-[#0077F7] hover:underline">
                        Forgot Password?
                    </a>
                </div>

                {{-- BUTTON --}}
                <button type="submit"
                        class="w-full py-3 bg-[#0077F7] hover:bg-[#0063CF] active:scale-[0.98]
                               text-white font-medium rounded-xl
                               shadow-[0_4px_14px_rgba(0,0,0,0.25)] transition-all duration-200">
                    Login
                </button>
            </form>

            <p class="text-center text-xs text-gray-600 mt-6">
                © {{ date('Y') }} Polysync. All Rights Reserved.
            </p>
        </div>
    </div>
</div>

{{-- JAVASCRIPT FOR PASSWORD TOGGLE --}}
<script>
function togglePassword() {
    const field = document.getElementById("passwordField");
    const icon = document.getElementById("eyeIcon");

    if (field.type === "password") {
        field.type = "text";
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3l18 18m-4.5-4.5A9.77 9.77 0 0112 19.5c-4.637 0-8.573-3.007-9.963-7.178a1.01 1.01 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c1.79 0 3.462.463 4.916 1.28M9.88 9.88a3 3 0 104.24 4.24"/>
        `;
    } else {
        field.type = "password";
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.637 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.637 0-8.573-3.007-9.963-7.178z"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        `;
    }
}
</script>

{{-- ANIMATION STYLES --}}
<style>
/* Remove floating logo animation completely */
/* (deleted animate-logoFloat and keyframes) */

@keyframes logoFade {
    from { opacity: 0; transform: scale(0.92); }
    to { opacity: 1; transform: scale(1); }
}
.animate-logoFade {
    animation: logoFade 1.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fadeIn {
    animation: fadeIn 1.2s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slideUp {
    animation: slideUp 1.2s ease;
}
</style>
@endsection
