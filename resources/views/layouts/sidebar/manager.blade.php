<div class="menu-section">Main</div>
<a href="{{ route('dashboard') }}">
    <div class="menu-item">
        <i data-lucide="layout-dashboard" class="menu-icon"></i> Dashboard
    </div>
</a>

<div class="menu-section">Management</div>
<a href="{{ route('projects.index') }}">
    <div class="menu-item">
        <i data-lucide="folder-kanban" class="menu-icon"></i> Projects
    </div>
</a>

<div class="menu-section">Attendance</div>
<a href="{{ route('attendance.manage') }}">
    <div class="menu-item">
        <i data-lucide="calendar-check" class="menu-icon"></i> Attendance Manager
    </div>
</a>
<a href="{{ route('attendance.scanner') }}">
    <div class="menu-item">
        <i data-lucide="qr-code" class="menu-icon"></i> QR Scanner
    </div>
</a>

{{--

<a href="{{ route('profile') }}" class="mt-6 block">
    <div class="menu-item">
        <i data-lucide="user" class="menu-icon"></i> Profile Details
    </div>
</a>

 --}}
