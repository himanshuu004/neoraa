<?php
$hour = (int)date('H');
if ($hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}
$userName = $_SESSION['username'] ?? 'User';
?>
<nav class="navbar sticky-top" style="z-index: 1000;">
    <div class="container-fluid px-3 px-md-4 d-flex align-items-center justify-content-between" style="min-height:64px;">

        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="<?php echo BASE_URL; ?>therapist/dashboard.php">
            <img src="<?php echo BASE_URL; ?>uploads/logo.png" alt="Neora Logo" style="height:52px;width:auto;object-fit:contain;">
            <div class="d-none d-sm-block" style="line-height:1.2;">
                <div style="font-family:'Cormorant Upright',serif;font-size:1.15rem;font-weight:600;color:#1A1A1A;letter-spacing:0.3px;"><?php echo SITE_NAME; ?></div>
                <div style="font-size:0.65rem;color:#df5589;font-weight:500;text-transform:uppercase;letter-spacing:1.5px;">Therapist Portal</div>
            </div>
        </a>

        <!-- Right: info + hamburger -->
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <div class="d-none d-md-flex flex-column align-items-end" style="line-height:1.3;">
                <span style="font-size:0.72rem;color:#777F81;"><?php echo $greeting; ?></span>
                <span style="font-size:0.8rem;color:#1A1A1A;font-weight:600;"><?php echo htmlspecialchars($userName); ?></span>
                <span id="navbarTime" style="font-size:0.65rem;color:#df5589;"></span>
            </div>
            <span class="d-none d-sm-inline-flex align-items-center px-2 py-1 rounded-pill"
                  style="background:rgba(0,168,150,0.1);color:#00877a;font-size:0.65rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;">
                Therapist
            </span>
            <button id="mobileMenuBtn" class="btn p-2" type="button" aria-label="Toggle menu"
                    style="border:1.5px solid rgba(223,85,137,0.3);border-radius:8px;color:#df5589;background:transparent;line-height:1;">
                <i class="fas fa-bars" style="font-size:1.1rem;"></i>
            </button>
        </div>
    </div>

    <!-- Slide-out Drawer -->
    <div id="menuOverlay" class="menu-overlay" style="display:none;">
        <div class="menu-overlay-content">
            <div class="menu-drawer-header">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="menu-greeting"><?php echo $greeting; ?></p>
                        <p class="menu-username"><?php echo htmlspecialchars($userName); ?></p>
                        <div class="menu-time">
                            <i class="fas fa-clock" style="font-size:0.65rem;"></i>
                            <span id="navbarTimeMobile"></span>
                        </div>
                    </div>
                    <div class="menu-drawer-close">
                        <button type="button" class="btn-close" id="closeMenuBtn" aria-label="Close"></button>
                    </div>
                </div>
                <div class="mt-2">
                    <span style="background:rgba(255,255,255,0.25);color:#fff;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:1.2px;padding:0.2rem 0.6rem;border-radius:20px;">
                        Therapist Portal
                    </span>
                </div>
            </div>

            <div class="menu-drawer-body">
                <a href="<?php echo BASE_URL; ?>therapist/dashboard.php#personal" class="nav-link-overlay" onclick="closeMenuOverlay()">
                    <i class="fas fa-user me-3"></i>Personal Info
                </a>
                <a href="<?php echo BASE_URL; ?>therapist/dashboard.php#timetable" class="nav-link-overlay" onclick="closeMenuOverlay()">
                    <i class="fas fa-calendar-alt me-3"></i>Session Timing
                </a>
                <hr class="menu-drawer-divider">
                <a href="<?php echo BASE_URL; ?>logout.php" class="nav-link-overlay text-danger">
                    <i class="fas fa-sign-out-alt me-3"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
(function() {
    function updateNavbarTime() {
        var opts = { timeZone:'Asia/Kolkata', hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true };
        var t = new Date().toLocaleString('en-IN', opts);
        var el = document.getElementById('navbarTime');
        var elM = document.getElementById('navbarTimeMobile');
        if (el) el.textContent = t;
        if (elM) elM.textContent = t;
    }
    updateNavbarTime();
    setInterval(updateNavbarTime, 1000);

    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('mobileMenuBtn');
        var overlay = document.getElementById('menuOverlay');
        var closeBtn = document.getElementById('closeMenuBtn');

        function openMenu() {
            if (!overlay) return;
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(function () { overlay.classList.add('active'); }, 10);
        }

        function closeMenu() {
            if (!overlay) return;
            overlay.classList.remove('active');
            setTimeout(function () {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }

        if (btn) btn.addEventListener('click', function (e) { e.stopPropagation(); openMenu(); });
        if (closeBtn) closeBtn.addEventListener('click', function (e) { e.stopPropagation(); closeMenu(); });
        if (overlay) overlay.addEventListener('click', function (e) { if (e.target === overlay) closeMenu(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay && overlay.style.display !== 'none') closeMenu();
        });
        window.closeMenuOverlay = closeMenu;
    });
}());
</script>
