{{-- =========================
    AUDIT SIDEBAR (READ ONLY)
========================= --}}

<div class="menu-section">Audit</div>

<a href="{{ route('audit.dashboard') }}">
    <div class="menu-item {{ request()->routeIs('audit.dashboard') ? 'menu-active' : '' }}">
        <i data-lucide="shield-check" class="menu-icon"></i>
        Audit Dashboard
    </div>
</a>

<div class="menu-section">Monitoring</div>

<a href="{{ route('expenses.index') }}">
    <div class="menu-item {{ request()->routeIs('expenses.*') ? 'menu-active' : '' }}">
        <i data-lucide="file-search" class="menu-icon"></i>
        Expense Logs
    </div>
</a>

<a href="{{ route('payments.index') }}">
    <div class="menu-item {{ request()->routeIs('payments.*') ? 'menu-active' : '' }}">
        <i data-lucide="credit-card" class="menu-icon"></i>
        Payment Logs
    </div>
</a>

<a href="{{ route('projects.index') }}">
    <div class="menu-item {{ request()->routeIs('projects.*') ? 'menu-active' : '' }}">
        <i data-lucide="folder" class="menu-icon"></i>
        Project Records
    </div>
</a>
