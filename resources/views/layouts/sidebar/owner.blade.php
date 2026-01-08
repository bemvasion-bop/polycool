    {{-- SIDEBAR --}}
<div class="menu-section">Main</div>
<a href="{{ route('dashboard') }}">
    <div class="menu-item {{ request()->routeIs('dashboard') ? 'menu-active' : '' }}">
        <i data-lucide="layout-dashboard" class="menu-icon"></i> Dashboard
    </div>
</a>

<div class="menu-section">Management</div>
<a href="{{ route('projects.index') }}">
    <div class="menu-item {{ request()->routeIs('projects.*') ? 'menu-active' : '' }}">
        <i data-lucide="folder-kanban" class="menu-icon"></i> Projects
    </div>
</a>
<a href="{{ route('quotations.index') }}">
    <div class="menu-item">
        <i data-lucide="file-text" class="menu-icon"></i> Quotations
    </div>
</a>
<a href="{{ route('clients.index') }}">
    <div class="menu-item">
        <i data-lucide="users" class="menu-icon"></i> Clients
    </div>
</a>
<a href="{{ route('employees.index') }}">
    <div class="menu-item">
        <i data-lucide="badge-check" class="menu-icon"></i> Employees
    </div>
</a>

<div class="menu-section">Finance</div>
<a href="{{ route('payroll.index') }}">
    <div class="menu-item">
        <i data-lucide="wallet" class="menu-icon"></i> Payroll
    </div>
</a>
<a href="{{ route('expenses.index') }}">
    <div class="menu-item">
        <i data-lucide="receipt" class="menu-icon"></i> Expenses
    </div>
</a>
<a href="{{ route('cashadvance.index') }}">
    <div class="menu-item">
        <i data-lucide="coins" class="menu-icon"></i> Cash Advances
    </div>
</a>
<a href="{{ route('payments.index') }}">
    <div class="menu-item">
        <i data-lucide="credit-card" class="menu-icon"></i> Payments
    </div>
</a>

<div class="menu-section">Operations</div>
<a href="{{ route('suppliers.index') }}">
    <div class="menu-item">
        <i data-lucide="truck" class="menu-icon"></i> Suppliers
    </div>
</a>
<a href="{{ route('materials.index') }}">
    <div class="menu-item">
        <i data-lucide="boxes" class="menu-icon"></i> Materials
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

<form action="{{ route('sync.all') }}" method="POST" class="mt-6">
    @csrf
    <button class="sync-btn">
        <i data-lucide="cloud-upload"></i>
        Sync to Cloud
    </button>
</form>
