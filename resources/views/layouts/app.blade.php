<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polysync System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r shadow-sm h-screen fixed">
        <div class="p-6 flex items-center space-x-3">
            <img src="/logo.png" class="h-8" alt="Logo">
            <span class="text-xl font-semibold">Polysync</span>
        </div>

        <nav class="mt-4">
            <ul class="space-y-1">

                <li>
                    <a href="{{ route('dashboard') }}"
                        class="block px-6 py-3 rounded-lg
                        {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="{{ route('quotations.index') }}"
                        class="block px-6 py-3 rounded-lg
                        {{ request()->routeIs('quotations.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        Quotations
                    </a>
                </li>

                <li>
                    <a href="{{ route('projects.index') }}"
                        class="block px-6 py-3 rounded-lg
                        {{ request()->routeIs('projects.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        Projects
                    </a>
                </li>

                <li>
                    <a href="{{ route('clients.index') }}"
                        class="block px-6 py-3 rounded-lg
                        {{ request()->routeIs('clients.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        Clients
                    </a>
                </li>

                @if(auth()->user()->role === 'owner' || auth()->user()->role === 'manager')
                    <li>
                        <a href="{{ route('employees.index') }}"
                            class="block px-6 py-3 rounded-lg
                            {{ request()->routeIs('employees.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                            Employees
                        </a>
                    </li>
                @endif

                <li>
                    <a href="#"
                        class="block px-6 py-3 rounded-lg text-gray-400 cursor-not-allowed">
                        Attendance Scanner
                    </a>
                </li>

                <li>
                    <a href="#"
                        class="block px-6 py-3 rounded-lg text-gray-400 cursor-not-allowed">
                        Payroll
                    </a>
                </li>

            </ul>
        </nav>

        <div class="absolute bottom-0 w-64 p-6 border-t">
            <div class="flex items-center justify-between">
                <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>


    <!-- MAIN CONTENT WRAPPER -->
    <div class="ml-64 p-10">
        @yield('content')
    </div>

</body>
</html>
