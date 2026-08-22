@php
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
$userName = $_SESSION['username'] ?? 'User';
@endphp

<header class="portal-topbar">
    <button type="button" class="portal-menu-btn" id="portalMenuBtn" aria-label="Open menu">
        <i class="fas fa-bars"></i>
    </button>
    <a class="portal-topbar-brand" href="{{ route('coordinator.dashboard') }}">
        <img src="{{ $baseUrl }}uploads/logo.png" alt="Neora">
        <span>{{ $siteName }}</span>
    </a>
    <span class="d-none d-sm-inline-flex align-items-center px-2 py-1 rounded-pill"
          style="background:rgba(223,85,137,0.1);color:#df5589;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;">
        Coordinator
    </span>
</header>

<div class="portal-backdrop" id="portalBackdrop"></div>

<aside class="portal-sidebar" id="portalSidebar">
<div class="portal-sidebar-brand">
        <a href="{{ route('coordinator.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none min-w-0 flex-grow-1">
            <img src="{{ $baseUrl }}uploads/logo.png" alt="Neora Logo">
            <div class="brand-text">
                <div class="brand-name">{{ $siteName }}</div>
                <div class="brand-role">Coordinator Portal</div>
            </div>
        </a>
        <button type="button" class="portal-sidebar-close" id="portalCloseBtn" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="portal-sidebar-user">
        <p class="user-greet">{{ $greeting }}</p>
        <p class="user-name">{{ $userName }}</p>
        <div class="user-meta">
            <span class="user-pill">Coordinator</span>
            <span class="user-time"><i class="fas fa-clock"></i> <span data-portal-time></span></span>
        </div>
    </div>

    <nav class="portal-sidebar-nav">
        <div class="portal-nav-label">Menu</div>
        <a href="{{ route('coordinator.dashboard') }}#personal" class="portal-nav-link" data-nav="personal">
            <i class="fas fa-user"></i> Personal Info
        </a>
        <a href="{{ route('admin.applications') }}" class="portal-nav-link" data-nav="applications">
            <i class="fas fa-file-alt"></i> Applications
        </a>
    </nav>

    <div class="portal-sidebar-footer">
        @include('portal.logout_button')
    </div>
</aside>

@include('portal.csrf_setup')
<script src="{{ $baseUrl }}assets/js/portal-sidebar.js"></script>
