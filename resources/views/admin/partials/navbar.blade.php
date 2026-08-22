@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
$userName = $_SESSION['username'] ?? 'User';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$home = $isAdmin ? route('admin.dashboard') : route('coordinator.dashboard');
@endphp

<header class="portal-topbar">
    <button type="button" class="portal-menu-btn" id="portalMenuBtn" aria-label="Open menu">
        <i class="fas fa-bars"></i>
    </button>
    <a class="portal-topbar-brand" href="{{ $home }}">
        <img src="{{ $baseUrl }}uploads/logo.png" alt="Neora">
        <span>{{ $siteName }}</span>
    </a>
    <span class="d-none d-sm-inline-flex align-items-center px-2 py-1 rounded-pill"
          style="background:rgba(223,85,137,0.1);color:#df5589;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;">
        {{ $isAdmin ? 'Admin' : 'Staff' }}
    </span>
</header>

<div class="portal-backdrop" id="portalBackdrop"></div>

<aside class="portal-sidebar" id="portalSidebar">
<div class="portal-sidebar-brand">
        <a href="{{ $home }}" class="d-flex align-items-center gap-2 text-decoration-none min-w-0 flex-grow-1">
            <img src="{{ $baseUrl }}uploads/logo.png" alt="Neora Logo">
            <div class="brand-text">
                <div class="brand-name">{{ $siteName }}</div>
                <div class="brand-role">{{ $isAdmin ? 'Admin Portal' : 'Staff Portal' }}</div>
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
            <span class="user-pill">{{ $isAdmin ? 'Admin' : 'Staff' }}</span>
            <span class="user-time"><i class="fas fa-clock"></i> <span data-portal-time></span></span>
        </div>
    </div>

    <nav class="portal-sidebar-nav">
        <div class="portal-nav-label">Account</div>
        <a href="{{ $home }}#personal" class="portal-nav-link" data-nav="personal">
            <i class="fas fa-user"></i> Personal Info
        </a>

        @if ($isAdmin)
            <div class="portal-nav-label">Clinic</div>
            <a href="{{ route('admin.dashboard') }}#therapists" class="portal-nav-link" data-nav="therapists">
                <i class="fas fa-users"></i> Therapists
            </a>
            <a href="{{ route('admin.dashboard') }}#kids" class="portal-nav-link" data-nav="kids">
                <i class="fas fa-child"></i> Kids
            </a>
            <a href="{{ route('admin.dashboard') }}#timetable" class="portal-nav-link" data-nav="timetable">
                <i class="fas fa-table"></i> Timetable
            </a>
            <a href="{{ route('admin.my-session') }}" class="portal-nav-link" data-nav="my-session">
                <i class="fas fa-calendar-check"></i> My Sessions
            </a>
            <a href="{{ route('admin.bookings') }}" class="portal-nav-link" data-nav="bookings">
                <i class="fas fa-calendar-alt"></i> Bookings
            </a>

            <div class="portal-nav-label">People</div>
            <a href="{{ route('admin.dashboard') }}#trainees" class="portal-nav-link" data-nav="trainees">
                <i class="fas fa-graduation-cap"></i> Trainees
            </a>
            <a href="{{ route('admin.dashboard') }}#coordinators" class="portal-nav-link" data-nav="coordinators">
                <i class="fas fa-user-tie"></i> Coordinators
            </a>
            <a href="{{ route('admin.applications') }}" class="portal-nav-link" data-nav="applications">
                <i class="fas fa-file-alt"></i> Applications
            </a>

            <div class="portal-nav-label">Content</div>
            <a href="{{ route('admin.dashboard') }}#noticeboard" class="portal-nav-link" data-nav="noticeboard">
                <i class="fas fa-bullhorn"></i> Notice Board
            </a>
            <a href="{{ route('admin.reviews') }}" class="portal-nav-link" data-nav="reviews">
                <i class="fas fa-star"></i> Reviews
            </a>
        @else
            <div class="portal-nav-label">Work</div>
            <a href="{{ route('admin.applications') }}" class="portal-nav-link" data-nav="applications">
                <i class="fas fa-file-alt"></i> Applications
            </a>
        @endif
    </nav>

    <div class="portal-sidebar-footer">
        @include('portal.logout_button')
    </div>
</aside>

@include('portal.csrf_setup')
<script src="{{ $baseUrl }}assets/js/portal-sidebar.js"></script>
