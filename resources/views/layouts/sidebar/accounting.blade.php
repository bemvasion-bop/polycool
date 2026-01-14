{{-- =========================
    ACCOUNTING SIDEBAR
========================= --}}

<div class="menu-section">Main</div>
<a href="{{ route('accounting.dashboard') }}">
    <div class="menu-item {{ request()->routeIs('accounting.dashboard') ? 'menu-active' : '' }}">
        <i data-lucide="layout-dashboard" class="menu-icon"></i>
        Dashboard
    </div>
</a>

<div class="menu-section">Finance</div>

<a href="{{ route('payroll.index') }}">
    <div class="menu-item {{ request()->routeIs('payroll.*') ? 'menu-active' : '' }}">
        <i data-lucide="wallet" class="menu-icon"></i>
        Payroll
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

<a href="{{ route('cashadvance.index') }}">
    <div class="menu-item {{ request()->routeIs('cashadvance.*') ? 'menu-active' : '' }}">
        <i data-lucide="coins" class="menu-icon"></i>
        Cash Advance Requests
    </div>
</a>

{{--

<div class="menu-section">Account</div>
  <a href="{{ route('profile') }}">
    <div class="menu-item {{ request()->routeIs('profile') ? 'menu-active' : '' }}">
            <i data-lucide="user-cog" class="menu-icon"></i>
        Profile Details
    </div>
</a>

--}}
