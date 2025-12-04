<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PolySync')</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.cdnfonts.com/css/sf-pro-display" rel="stylesheet">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", Helvetica, Arial, sans-serif;
            overflow: hidden; /* IMPORTANT – no window scrolling */
        }

        /* ===========================================================
           🌈 BACKGROUND (Matching login page aesthetic)
        ===========================================================*/
        .polysync-bg {
            position: fixed;
            inset: 0;
            z-index: -30;
        }
        .polysync-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom right,
                #AFC8FF80, #CBB9FF80, #A8E0FF80
            );
            opacity: .75;
        }
        .sphere-1 {
            position: absolute;
            width: 700px; height: 700px;
            background: #7A68FF40;
            border-radius: 50%;
            filter: blur(150px);
            top: -150px; left: -150px;
            z-index: -20;
        }
        .sphere-2 {
            position: absolute;
            width: 620px; height: 620px;
            background: #00C2FF35;
            border-radius: 50%;
            filter: blur(160px);
            top: 260px; right: -200px;
            z-index: -20;
        }
        .polysync-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.20);
            mix-blend-mode: screen;
            backdrop-filter: blur(12px);
        }

        /* ===========================================================
           🌈 SIDEBAR — Glass Panel
        ===========================================================*/
        .sidebar-container {
            height: calc(100vh - 2rem);
            width: 250px;

            margin: 1rem;
            padding: 1.3rem;

            border-radius: 32px;
            backdrop-filter: blur(28px);
            background: rgba(255,255,255,0.30);
            border: 1px solid rgba(255,255,255,0.45);
            box-shadow: 0 12px 50px rgba(0,0,0,0.22);

            overflow-y: auto;
        }

        .sidebar-container::-webkit-scrollbar { display: none; }

        .menu-section {
            font-size: 11px;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: .11em;
            margin: 18px 0 6px 4px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 999px;
            color: #1F1F1F;
            font-size: .92rem;
            transition: .18s ease;
        }
        .menu-item:hover {
            background: rgba(255,255,255,0.45);
        }
        .menu-active {
            background: rgba(255,255,255,0.60);
            font-weight: 600;
            color: #0053D6;
        }

        .menu-icon { width: 20px; height: 20px; }

        /* ===========================================================
           🌈 MAIN PANEL — Fixed Window + Scrollable Inside
        ===========================================================*/
        .main-panel {
            flex: 1;
            height: calc(100vh - 2rem); /* fixed height */

            margin: 1rem 1rem 1rem 0;
            border-radius: 32px;

            backdrop-filter: blur(28px);
            background: rgba(255,255,255,0.28);
            border: 1px solid rgba(255,255,255,0.45);
            box-shadow: 0 12px 50px rgba(0,0,0,0.22);

            padding: 2rem;
            display: flex;
            flex-direction: column;

            overflow: hidden; /* DO NOT SCROLL PANEL */
        }

        /* Only this area scrolls */
        .main-panel-scroll {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 10px;
            margin-top: 1rem;
        }

        .main-panel-scroll::-webkit-scrollbar {
            width: 7px;
        }
        .main-panel-scroll::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.22);
            border-radius: 10px;
        }
    </style>
</head>



<body>

{{-- Background --}}
<div class="polysync-bg"></div>
<div class="sphere-1"></div>
<div class="sphere-2"></div>

<div class="flex">

<<<<<<< HEAD
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
=======
    {{-- 🌈 SIDEBAR --}}
    <aside class="sidebar-container">
>>>>>>> 1c501e3504de40feb6bb86d38e1175668b1b812d

        <div class="flex items-center gap-2 mb-8">
            <img src="/logo.png" class="h-8">
            <span class="text-lg font-semibold">PolySync</span>
        </div>

        <div class="menu-section">Main</div>

        <a href="{{ route('dashboard') }}">
            <div class="menu-item {{ request()->routeIs('dashboard') ? 'menu-active' : '' }}">
                <i data-lucide="layout-dashboard" class="menu-icon"></i>
                Dashboard
            </div>
        </a>

        <div class="menu-section">Management</div>

        <a href="{{ route('projects.index') }}">
            <div class="menu-item {{ request()->routeIs('projects.*') ? 'menu-active' : '' }}">
                <i data-lucide="folder-kanban" class="menu-icon"></i>
                Projects
            </div>
        </a>

        <a href="{{ route('quotations.index') }}">
            <div class="menu-item {{ request()->routeIs('quotations.*') ? 'menu-active' : '' }}">
                <i data-lucide="file-text" class="menu-icon"></i>
                Quotations
            </div>
        </a>

        <a href="{{ route('clients.index') }}">
            <div class="menu-item {{ request()->routeIs('clients.*') ? 'menu-active' : '' }}">
                <i data-lucide="users" class="menu-icon"></i>
                Clients
            </div>
        </a>

        <a href="{{ route('employees.index') }}">
            <div class="menu-item {{ request()->routeIs('employees.*') ? 'menu-active' : '' }}">
                <i data-lucide="badge-check" class="menu-icon"></i>
                Employees
            </div>
        </a>

        <div class="menu-section">Finance</div>

        <a href="{{ route('payroll.index') }}">
            <div class="menu-item {{ request()->routeIs('payroll.*') ? 'menu-active' : '' }}">
                <i data-lucide="wallet" class="menu-icon"></i>
                Payroll
            </div>
        </a>

        <a href="{{ route('cashadvance.index') }}">
            <div class="menu-item {{ request()->routeIs('cashadvance.*') ? 'menu-active' : '' }}">
                <i data-lucide="coins" class="menu-icon"></i>
                Cash Advances
            </div>
        </a>

        <a href="{{ route('expenses.index') }}">
            <div class="menu-item {{ request()->routeIs('expenses.*') ? 'menu-active' : '' }}">
                <i data-lucide="receipt" class="menu-icon"></i>
                Expenses
            </div>
        </a>

        <a href="{{ route('payments.index') }}">
            <div class="menu-item {{ request()->routeIs('payments.*') ? 'menu-active' : '' }}">
                <i data-lucide="credit-card" class="menu-icon"></i>
                Payments
            </div>
        </a>

        <div class="menu-section">Operations</div>

        <a href="{{ route('suppliers.index') }}">
            <div class="menu-item {{ request()->routeIs('suppliers.*') ? 'menu-active' : '' }}">
                <i data-lucide="truck" class="menu-icon"></i>
                Suppliers
            </div>
        </a>

        <a href="{{ route('materials.index') }}">
            <div class="menu-item {{ request()->routeIs('materials.*') ? 'menu-active' : '' }}">
                <i data-lucide="boxes" class="menu-icon"></i>
                Materials
            </div>
        </a>

        <div class="menu-section">Attendance</div>

        <a href="{{ route('attendance.manage') }}">
            <div class="menu-item {{ request()->routeIs('attendance.manage') ? 'menu-active' : '' }}">
                <i data-lucide="clipboard-list" class="menu-icon"></i>
                Attendance Manager
            </div>
        </a>

        <a href="{{ route('attendance.scanner') }}">
            <div class="menu-item {{ request()->routeIs('attendance.scanner') ? 'menu-active' : '' }}">
                <i data-lucide="scan-line" class="menu-icon"></i>
                QR Scanner
            </div>
        </a>

        <div class="mt-12 border-t border-white/40 pt-4">
            <p class="text-xs text-gray-700">{{ auth()->user()->system_role }}</p>
            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="mt-2 text-red-500 text-sm hover:underline">Logout</button>
            </form>
        </div>

    </aside>

    {{-- 🌈 MAIN PANEL --}}
    <main class="main-panel">

       {{-- HEADER (NOT SCROLLING) --}}
<div class="flex items-center justify-between">

    {{-- PAGE TITLE --}}
    <div>
        @yield('page-header')
    </div>

    {{-- PROFILE BUBBLE --}}
    <div class="flex items-center gap-3">
        <div class="h-8 w-8 rounded-full bg-gradient-to-br 
                    from-indigo-400 to-sky-400 flex items-center justify-center
                    text-xs font-semibold text-white">
            {{ strtoupper(substr(auth()->user()->name,0,1)) }}
        </div>

        <div class="hidden lg:flex flex-col">
            <span class="text-xs text-gray-600">Signed in as</span>
            <span class="text-sm font-semibold text-gray-800">
                {{ auth()->user()->full_name }}
            </span>
        </div>
    </div>

</div>


        {{-- SCROLLABLE CONTENT --}}
        <div class="main-panel-scroll">
            @yield('content')
        </div>

    </main>

    <script src="{{ asset('js/offline-sync.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!navigator.onLine) {
            document.body.insertAdjacentHTML('afterbegin',
                '<div style="background:#c62828;color:white;padding:8px;text-align:center;">OFFLINE MODE</div>'
            );
        }
    });
    </script>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    lucide.createIcons();
});
</script>








</body>
</html>
