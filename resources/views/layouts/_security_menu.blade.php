<!-- Security Menu Item -->
<li class="menu-item {{ request()->routeIs('dashboard.login-attempts.*') ? 'active' : '' }}">
    <a href="{{ route('dashboard.login-attempts.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-shield-lock"></i>
        <div>تتبع محاولات تسجيل الدخول</div>
    </a>
</li>
