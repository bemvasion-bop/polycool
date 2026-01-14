{{-- =========================
    EMPLOYEE SIDEBAR
========================= --}}

{{-- MAIN --}}
<div class="menu-section">Main</div>

<a href="{{ route('employee.dashboard') }}">
    <div class="menu-item {{ request()->routeIs('employee.dashboard') ? 'menu-active' : '' }}">
        <i data-lucide="layout-dashboard" class="menu-icon"></i>
        Dashboard
    </div>
</a>

<a href="{{ route('projects.index') }}">
    <div class="menu-item {{ request()->routeIs('projects.*') ? 'menu-active' : '' }}">
        <i data-lucide="folder-kanban" class="menu-icon"></i>
        My Projects
    </div>
</a>

{{-- ATTENDANCE --}}
<div class="menu-section">Attendance</div>

<a href="{{ route('attendance.index') }}">
    <div class="menu-item {{ request()->routeIs('attendance.index') ? 'menu-active' : '' }}">
        <i data-lucide="calendar-check" class="menu-icon"></i>
        My DTR
    </div>
</a>

<a href="{{ route('attendance.myqr') }}">
    <div class="menu-item {{ request()->routeIs('attendance.myqr') ? 'menu-active' : '' }}">
        <i data-lucide="qr-code" class="menu-icon"></i>
        My QR Code
    </div>
</a>

{{-- PAYROLL --}}
<div class="menu-section">Payroll</div>

<a href="{{ route('employee.payslips') }}">
    <div class="menu-item {{ request()->routeIs('employee.payslips*') ? 'menu-active' : '' }}">
        <i data-lucide="wallet" class="menu-icon"></i>
        My Payslips
    </div>
</a>

{{-- REQUESTS --}}
<div class="menu-section">Requests</div>

<a href="{{ route('cashadvance.index') }}">
    <div class="menu-item {{ request()->routeIs('cashadvance.*') ? 'menu-active' : '' }}">
        <i data-lucide="coins" class="menu-icon"></i>
        Cash Advance
    </div>
</a>

{{-- PROFILE
<div class="menu-section">Account</div>

<a href="{{ route('employee.profile') }}">
    <div class="menu-item {{ request()->routeIs('employee.profile') ? 'menu-active' : '' }}">
        <i data-lucide="user" class="menu-icon"></i>
        Profile Details
    </div>
</a>

--}}
