<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'PolySync')</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        /* GLOBAL POLYSYNC BACKGROUND */
        .polysync-bg {
            position: fixed;
            inset: 0;
            z-index: -10;
        }

        .polysync-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom right,
                rgba(175, 200, 255, 0.65),
                rgba(203, 185, 255, 0.65),
                rgba(168, 224, 255, 0.65)
            );
            z-index: -2;
        }

        .polysync-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(18px);
            z-index: -1;
        }

        /* Apple sidebar hover styling */
        .menu-item {
            border-radius: 14px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all .18s ease;
        }
        .menu-item:hover {
            background: rgba(0,0,0,0.08);
        }

        .menu-active {
            background: rgba(0,0,0,0.12);
            font-weight: 600;
        }

        .menu-section {
            font-size: 11px;
            text-transform: uppercase;
            color: #7b7b7b;
            margin: 20px 0 6px 10px;
        }

        /* Hide scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            display: none;
        }
        .sidebar-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body>

{{-- GLOBAL BACKGROUND (APPLIES TO ALL PAGES) --}}
<div class="polysync-bg"></div>

<div class="flex">

    {{-- SIDEBAR --}}
    @auth
    <aside class="fixed z-30">

        <div class="h-[94vh] w-[300px] rounded-3xl mt-4 ml-4
                    backdrop-blur-2xl bg-white/70 border border-white/40 shadow-[0_8px_40px_rgba(0,0,0,0.12)]
                    p-6 sidebar-scroll overflow-y-auto">

            {{-- BRAND --}}
            <div class="flex items-center space-x-3 mb-6">
                <img src="/logo.png" class="h-10 opacity-90">
                <span class="text-xl font-semibold text-gray-800">PolySync</span>
            </div>

            {{-- MENU --}}
            <div class="menu-section">Main</div>

            <a href="{{ route('dashboard') }}">
                <div class="menu-item {{ request()->routeIs('dashboard') ? 'menu-active' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </div>
            </a>

            <div class="menu-section">Management</div>

            @if(role('owner') || role('manager'))
            <a href="{{ route('projects.index') }}">
                <div class="menu-item {{ request()->routeIs('projects.*') ? 'menu-active' : '' }}">
                    <i data-lucide="folder-kanban" class="w-5 h-5"></i> Projects
                </div>
            </a>
            @endif

            @if(role('owner'))
            <a href="{{ route('quotations.index') }}">
                <div class="menu-item {{ request()->routeIs('quotations.*') ? 'menu-active' : '' }}">
                    <i data-lucide="file-text" class="w-5 h-5"></i> Quotations
                </div>
            </a>

            <a href="{{ route('clients.index') }}">
                <div class="menu-item {{ request()->routeIs('clients.*') ? 'menu-active' : '' }}">
                    <i data-lucide="users" class="w-5 h-5"></i> Clients
                </div>
            </a>

            <a href="{{ route('employees.index') }}">
                <div class="menu-item {{ request()->routeIs('employees.*') ? 'menu-active' : '' }}">
                    <i data-lucide="badge-check" class="w-5 h-5"></i> Employees
                </div>
            </a>
            @endif


            <div class="menu-section">Finance</div>

            @if(role('owner') || role('accounting'))
            <a href="{{ route('payroll.index') }}">
                <div class="menu-item {{ request()->routeIs('payroll.*') ? 'menu-active' : '' }}">
                    <i data-lucide="wallet" class="w-5 h-5"></i> Payroll
                </div>
            </a>

            <a href="{{ route('cashadvance.index') }}">
                <div class="menu-item {{ request()->routeIs('cashadvance.*') ? 'menu-active' : '' }}">
                    <i data-lucide="coins" class="w-5 h-5"></i> Cash Advances
                </div>
            </a>

            <a href="{{ route('expenses.index') }}">
                <div class="menu-item {{ request()->routeIs('expenses.*') ? 'menu-active' : '' }}">
                    <i data-lucide="receipt" class="w-5 h-5"></i> Expenses
                </div>
            </a>

            <a href="{{ route('payments.index') }}">
                <div class="menu-item {{ request()->routeIs('payments.*') ? 'menu-active' : '' }}">
                    <i data-lucide="credit-card" class="w-5 h-5"></i> Payments
                </div>
            </a>
            @endif


            <div class="menu-section">Operations</div>

            @if(role('owner'))
            <a href="{{ route('suppliers.index') }}">
                <div class="menu-item {{ request()->routeIs('suppliers.*') ? 'menu-active' : '' }}">
                    <i data-lucide="truck" class="w-5 h-5"></i> Suppliers
                </div>
            </a>

            <a href="{{ route('materials.index') }}">
                <div class="menu-item {{ request()->routeIs('materials.*') ? 'menu-active' : '' }}">
                    <i data-lucide="boxes" class="w-5 h-5"></i> Materials
                </div>
            </a>
            @endif


            <div class="menu-section">Attendance</div>

            <a href="{{ route('attendance.manage') }}">
                <div class="menu-item {{ request()->routeIs('attendance.manage') ? 'menu-active' : '' }}">
                    <i data-lucide="clipboard-list" class="w-5 h-5"></i> Attendance Manager
                </div>
            </a>

            <a href="{{ route('attendance.scanner') }}">
                <div class="menu-item {{ request()->routeIs('attendance.scanner') ? 'menu-active' : '' }}">
                    <i data-lucide="scan-line" class="w-5 h-5"></i> QR Scanner
                </div>
            </a>

            {{-- FOOTER --}}
            <div class="mt-10 pt-6 border-t border-gray-300/40">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium text-sm">{{ auth()->user()->full_name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-600 hover:underline text-sm">Logout</button>
                    </form>
                </div>
            </div>

        </div>
    </aside>
    @endauth


    {{-- MAIN CONTENT --}}
    <main class="flex-1 ml-[330px] p-8">
        @yield('page-header')
        @yield('content')
    </main>

</div>

<script>
    lucide.createIcons();
</script>

</body>
</html> 
