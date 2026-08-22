(function () {
    document.body.classList.add('portal-body');

    var sidebar = document.getElementById('portalSidebar');
    var backdrop = document.getElementById('portalBackdrop');
    var openBtn = document.getElementById('portalMenuBtn');
    var closeBtn = document.getElementById('portalCloseBtn');
    var mq = window.matchMedia('(min-width: 992px)');

    function isDesktop() {
        return mq.matches;
    }

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('open');
        if (backdrop && !isDesktop()) {
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSidebar() {
        if (!sidebar) return;
        if (isDesktop()) return;
        sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    function syncForViewport() {
        if (!sidebar) return;
        if (isDesktop()) {
            sidebar.classList.add('open');
            if (backdrop) backdrop.classList.remove('show');
            document.body.style.overflow = '';
        } else {
            sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    function markActive() {
        var path = (window.location.pathname || '').replace(/\/+$/, '');
        var hash = (window.location.hash || '').replace('#', '');
        var links = document.querySelectorAll('.portal-nav-link[data-nav]');
        var matched = false;

        links.forEach(function (link) {
            link.classList.remove('active');
        });

        if (path.indexOf('/admin/applications') !== -1) {
            setActive('applications');
            matched = true;
        } else if (path.indexOf('/admin/bookings') !== -1) {
            setActive('bookings');
            matched = true;
        } else if (path.indexOf('/admin/reviews') !== -1) {
            setActive('reviews');
            matched = true;
        } else if (path.indexOf('/admin/my-session') !== -1) {
            setActive('my-session');
            matched = true;
        }

        if (!matched) {
            setActive(hash || 'personal');
        }

        function setActive(key) {
            links.forEach(function (link) {
                if (link.getAttribute('data-nav') === key) {
                    link.classList.add('active');
                }
            });
        }
    }

    function updateTimes() {
        var opts = { timeZone: 'Asia/Kolkata', hour: '2-digit', minute: '2-digit', hour12: true };
        var t = new Date().toLocaleString('en-IN', opts);
        document.querySelectorAll('[data-portal-time]').forEach(function (el) {
            el.textContent = t;
        });
    }

    if (openBtn) openBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        openSidebar();
    });
    if (closeBtn) closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closeSidebar();
    });
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    document.querySelectorAll('.portal-nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (!isDesktop()) closeSidebar();
        });
    });

    if (typeof mq.addEventListener === 'function') {
        mq.addEventListener('change', syncForViewport);
    } else if (typeof mq.addListener === 'function') {
        mq.addListener(syncForViewport);
    }

    window.addEventListener('hashchange', markActive);
    window.closeMenuOverlay = closeSidebar;

    syncForViewport();
    markActive();
    updateTimes();
    setInterval(updateTimes, 1000);
}());
