<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PolySync Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-full flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-8 space-y-6">
        
        {{-- Logo + Title --}}
        <div class="text-center space-y-2">
            <div class="flex items-center justify-center">
                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white text-xl font-bold">
                    P
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">PolySync</h1>
            <p class="text-gray-500 text-sm">Smart Resource & Project Management</p>
        </div>

        {{-- Login Form --}}
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" required
                    class="mt-1 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required
                    class="mt-1 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    <span>Remember me</span>
                </label>
            </div>

            {{-- Login Button --}}
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition">
                Login
            </button>
        </form>

        {{-- Footer --}}
        <div class="text-center text-xs text-gray-400 pt-4">
            © {{ date('Y') }} PolySync. All Rights Reserved.
        </div>
    </div>

</body>
</html>
