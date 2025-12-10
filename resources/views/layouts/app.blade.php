<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <title>@yield('title', 'PolySync')</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.cdnfonts.com/css/sf-pro-display" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", Helvetica, Arial, sans-serif;
            overflow: hidden;
        }

        .polysync-bg {
            position: fixed;
            inset: 0;
            z-index: -30;
        }
        .polysync-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom right,#AFC8FF80,#CBB9FF80,#A8E0FF80);
            opacity: .75;
        }
        .sphere-1 {
            position: absolute;
            width: 700px; height: 700px;
            background:#7A68FF40;
            border-radius:50%;
            filter:blur(150px);
            top:-150px; left:-150px;
            z-index:-20;
        }
        .sphere-2 {
            position: absolute;
            width:620px; height:620px;
            background:#00C2FF35;
            border-radius:50%;
            filter:blur(150px);
            top:260px; right:-200px;
            z-index:-20;
        }

        /* 🌈 Sidebar */
        .sidebar-container {
            height: calc(100vh - 2rem);
            width: 250px;
            margin: 1rem;
            padding: 1.3rem;
            border-radius: 32px;
            backdrop-filter: blur(28px);
            background: rgba(255,255,255,0.30);
            border:1px solid rgba(255,255,255,0.45);
            box-shadow:0 12px 50px rgba(0,0,0,0.22);
            overflow-y:auto;
        }
        .sidebar-container::-webkit-scrollbar { display:none; }

        .menu-section {
            font-size:11px; text-transform:uppercase;
            color:#9ca3af; letter-spacing:.11em;
            margin:18px 0 6px 4px;
        }
        .menu-item {
            display:flex; align-items:center;
            gap:12px; padding:10px 14px;
            border-radius:50px;
            color:#1F1F1F; font-size:.92rem;
            transition:.18s ease;
        }
        .menu-item:hover { background:rgba(255,255,255,0.45); }
        .menu-active {
            background:rgba(255,255,255,0.60);
            font-weight:600; color:#0053D6;
        }
        .menu-icon { width:20px; height:20px; }

        /* 🌈 MAIN PANEL */
        .main-panel {
            flex:1;
            height:calc(100vh - 2rem);
            margin:1rem 1rem 1rem 0;
            border-radius:32px;
            backdrop-filter:blur(28px);
            background:rgba(255,255,255,0.28);
            border:1px solid rgba(255,255,255,0.45);
            box-shadow:0 12px 50px rgba(0,0,0,0.22);
            padding:2rem;
            display:flex; flex-direction:column;
            overflow:hidden;
        }
        .main-panel-scroll {
            flex-grow:1;
            overflow-y:auto;
            overflow-x:hidden;
            padding-right:10px;
            margin-top:1rem;
        }
        .main-panel-scroll::-webkit-scrollbar { width:7px; }
        .main-panel-scroll::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.22);
            border-radius:10px;
        }

        /* 🌈 SYNC BUTTON */
        .sync-btn {
            width:100%;
            padding:12px;
            border-radius:50px;
            font-size:.9rem;
            font-weight:600;
            color:white;
            background:linear-gradient(to right,#a855f7,#6366f1);
            display:flex; align-items:center; justify-content:center;
            gap:8px;
            transition:.25s ease;
        }
        .sync-btn:hover { opacity:.92; }


        .toast-glass {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 14px 22px;
            border-radius: 22px;
            background: rgba(255,255,255,0.45);
            backdrop-filter: blur(18px) saturate(180%);
            -webkit-backdrop-filter: blur(18px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.55);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            font-size: 14px;
            color: #0a0a0a;
            animation: slideIn .35s ease, fadeOut .35s ease 3.5s forwards;
            z-index: 9999;
        }
        .toast-danger {
            border-color: rgba(255,100,100,0.75);
            color: #7f1d1d;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeOut {
            to   { opacity: 0; transform: translateX(20px); }
        }

    </style>
</head>

<body>

<div class="polysync-bg"></div>
<div class="sphere-1"></div>
<div class="sphere-2"></div>

<div class="flex">

    {{-- SIDEBAR --}}
    <aside class="sidebar-container">

        <div class="flex items-center gap-2 mb-8">
            <img src="/logo.png" class="h-8">
            <span class="text-lg font-semibold">PolySync</span>
        </div>

        <div class="menu-section">Main</div>
        <a href="{{ route('dashboard') }}">
            <div class="menu-item {{ request()->routeIs('dashboard') ? 'menu-active' : '' }}">
                <i data-lucide="layout-dashboard" class="menu-icon"></i> Dashboard
            </div>
        </a>

        <div class="menu-section">Management</div>
        @if(role('owner') || role('manager'))
        <a href="{{ route('projects.index') }}">
            <div class="menu-item {{ request()->routeIs('projects.*') ? 'menu-active' : '' }}">
                <i data-lucide="folder-kanban" class="menu-icon"></i> Projects
            </div>
        </a>
        @endif

        @if(role('owner'))
        <a href="{{ route('quotations.index') }}">
            <div class="menu-item {{ request()->routeIs('quotations.*') ? 'menu-active' : '' }}">
                <i data-lucide="file-text" class="menu-icon"></i> Quotations
            </div>
        </a>
        <a href="{{ route('clients.index') }}">
            <div class="menu-item {{ request()->routeIs('clients.*') ? 'menu-active' : '' }}">
                <i data-lucide="users" class="menu-icon"></i> Clients
            </div>
        </a>
        <a href="{{ route('employees.index') }}">
            <div class="menu-item {{ request()->routeIs('employees.*') ? 'menu-active' : '' }}">
                <i data-lucide="badge-check" class="menu-icon"></i> Employees
            </div>
        </a>
        @endif

        <div class="menu-section">Finance</div>
        @if(role('owner') || role('accounting'))
        <a href="{{ route('payroll.index') }}">
            <div class="menu-item {{ request()->routeIs('payroll.*') ? 'menu-active' : '' }}">
                <i data-lucide="wallet" class="menu-icon"></i> Payroll
            </div>
        </a>
        <a href="{{ route('cashadvance.index') }}">
            <div class="menu-item {{ request()->routeIs('cashadvance.*') ? 'menu-active' : '' }}">
                <i data-lucide="coins" class="menu-icon"></i> Cash Advances
            </div>
        </a>
        <a href="{{ route('expenses.index') }}">
            <div class="menu-item {{ request()->routeIs('expenses.*') ? 'menu-active' : '' }}">
                <i data-lucide="receipt" class="menu-icon"></i> Expenses
            </div>
        </a>
        <a href="{{ route('payments.index') }}">
            <div class="menu-item {{ request()->routeIs('payments.*') ? 'menu-active' : '' }}">
                <i data-lucide="credit-card" class="menu-icon"></i> Payments
            </div>
        </a>
        @endif

        <div class="menu-section">Operations</div>
        @if(role('owner'))
        <a href="{{ route('suppliers.index') }}">
            <div class="menu-item {{ request()->routeIs('suppliers.*') ? 'menu-active' : '' }}">
                <i data-lucide="truck" class="menu-icon"></i> Suppliers
            </div>
        </a>
        <a href="{{ route('materials.index') }}">
            <div class="menu-item {{ request()->routeIs('materials.*') ? 'menu-active' : '' }}">
                <i data-lucide="boxes" class="menu-icon"></i> Materials
            </div>
        </a>
        @endif

        <div class="menu-section">Attendance</div>
        <a href="{{ route('attendance.manage') }}">
            <div class="menu-item {{ request()->routeIs('attendance.manage') ? 'menu-active' : '' }}">
                <i data-lucide="clipboard-list" class="menu-icon"></i> Attendance Manager
            </div>
        </a>
        <a href="{{ route('attendance.scanner') }}">
            <div class="menu-item {{ request()->routeIs('attendance.scanner') ? 'menu-active' : '' }}">
                <i data-lucide="scan-line" class="menu-icon"></i> QR Scanner
            </div>
        </a>

        {{-- 🌩️ SYNC TO CLOUD BUTTON — OWNER ONLY --}}
        @if(role('owner'))
        <form action="{{ route('sync.all') }}" method="POST" style="width:100%;">
            @csrf
            <button type="submit" class="w-full text-white bg-purple-600 hover:bg-purple-700
                font-semibold px-4 py-3 rounded-xl transition">
                <i class="icon-cloud"></i> Sync to Cloud
            </button>
        </form>
        @endif

        {{-- EMPLOYEE ONLY: My Profile --}}
        @if(auth()->user()->system_role == 'owner' || auth()->user()->system_role == 'manager')
            <a href="{{ route('attendance.manage') }}" class="sidebar-item">
                Attendance Manager
            </a>
        @elseif(auth()->user()->system_role == 'employee')
            <a href="{{ route('employee.attendance') }}" class="sidebar-item">
                My Attendance
            </a>
        @endif


        {{-- User Info --}}
        <div class="mt-8 border-t border-white/40 pt-4">
            <p class="text-xs text-gray-700">{{ auth()->user()->system_role }}</p>
            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="mt-2 text-red-500 text-sm hover:underline">Logout</button>
            </form>
        </div>

    </aside>

    {{-- MAIN PANEL --}}
    <main class="main-panel">

        {{-- FIXED HEADER --}}
        <div class="flex items-center justify-between">
            @yield('page-header')
            <div></div>
        </div>

        {{-- SCROLL CONTENT --}}
        <div class="main-panel-scroll">
            @yield('content')
        </div>

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', ()=> lucide.createIcons());
</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2400,
        timerProgressBar: true,
        background: 'rgba(255,255,255,0.55)',
        color: '#111',
        iconColor: '#4b5563',
        customClass: {
            popup: 'backdrop-blur-xl shadow-xl border border-white/30 rounded-2xl px-5 py-3'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        })
    @endif

    @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: "{{ session('error') }}"
        })
    @endif

    @if(session('warning'))
        Toast.fire({
            icon: 'warning',
            title: "{{ session('warning') }}"
        })
    @endif

});
</script>


<script>
    window.phTimeNow = () => {
        return new Date().toLocaleString("en-PH", {
            timeZone: "Asia/Manila"
        });
    };
</script>


</body>
</html>
