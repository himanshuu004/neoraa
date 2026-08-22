<?php
// Navbar Component, Mellow-inspired redesign (top bar + primary nav)
$navBase = $baseUrl;
?>

<!-- Inline SVG symbol defs (icons used across the page) -->
<svg xmlns="http://www.w3.org/2000/svg" style="display:none;">
  <symbol id="location" viewBox="0 0 24 24">
    <path fill="currentColor" fill-rule="evenodd"
      d="M3.25 10.143C3.25 5.244 7.155 1.25 12 1.25c4.845 0 8.75 3.994 8.75 8.893c0 2.365-.674 4.905-1.866 7.099c-1.19 2.191-2.928 4.095-5.103 5.112a4.2 4.2 0 0 1-3.562 0c-2.175-1.017-3.913-2.92-5.103-5.112c-1.192-2.194-1.866-4.734-1.866-7.099M12 2.75c-3.992 0-7.25 3.297-7.25 7.393c0 2.097.603 4.392 1.684 6.383c1.082 1.993 2.612 3.624 4.42 4.469a2.7 2.7 0 0 0 2.291 0c1.809-.845 3.339-2.476 4.421-4.469c1.081-1.99 1.684-4.286 1.684-6.383c0-4.096-3.258-7.393-7.25-7.393m0 5a2.25 2.25 0 1 0 0 4.5a2.25 2.25 0 0 0 0-4.5M8.25 10a3.75 3.75 0 1 1 7.5 0a3.75 3.75 0 0 1-7.5 0"
      clip-rule="evenodd"/>
  </symbol>
  <symbol id="phone" viewBox="0 0 24 24">
    <path fill="currentColor" fill-rule="evenodd"
      d="M6.007 3.407c1.68-1.68 4.516-1.552 5.686.544l.649 1.163c.763 1.368.438 3.095-.68 4.227a.63.63 0 0 0-.104.337c-.013.256.078.849.997 1.767c.918.918 1.51 1.01 1.767.997a.63.63 0 0 0 .337-.104c1.131-1.118 2.859-1.443 4.227-.68l1.163.65c2.096 1.17 2.224 4.004.544 5.685c-.899.898-2.093 1.697-3.498 1.75c-2.08.079-5.536-.459-8.958-3.88c-3.421-3.422-3.959-6.877-3.88-8.958c.053-1.405.852-2.6 1.75-3.498z"
      clip-rule="evenodd"/>
  </symbol>
  <symbol id="email" viewBox="0 0 24 24">
    <path fill="currentColor" fill-rule="evenodd"
      d="M9.944 3.25h4.112c1.838 0 3.294 0 4.433.153c1.172.158 2.121.49 2.87 1.238c.748.749 1.08 1.698 1.238 2.87c.153 1.14.153 2.595.153 4.433v.112c0 1.838 0 3.294-.153 4.433c-.158 1.172-.49 2.121-1.238 2.87c-.749.748-1.698 1.08-2.87 1.238c-1.14.153-2.595.153-4.433.153H9.944c-1.838 0-3.294 0-4.433-.153c-1.172-.158-2.121-.49-2.87-1.238c-.748-.749-1.08-1.698-1.238-2.87c-.153-1.14-.153-2.595-.153-4.433v-.112c0-1.838 0-3.294.153-4.433c.158-1.172.49-2.121 1.238-2.87c.749-.748 1.698-1.08 2.87-1.238c1.14-.153 2.595-.153 4.433-.153M5.71 4.89c-1.006.135-1.586.389-2.01.812c-.422.423-.676 1.003-.811 2.009c-.138 1.028-.14 2.382-.14 4.289c0 1.907.002 3.262.14 4.29c.135 1.005.389 1.585.812 2.008c.423.423 1.003.677 2.009.812c1.028.138 2.382.14 4.289.14h4c1.907 0 3.262-.002 4.29-.14c1.005-.135 1.585-.389 2.008-.812c.423-.423.677-1.003.812-2.009c.138-1.028.14-2.382.14-4.289c0-1.907-.002-3.261-.14-4.29c-.135-1.005-.389-1.585-.812-2.008c-.423-.423-1.003-.677-2.009-.812c-1.027-.138-2.382-.14-4.289-.14h-4c-1.907 0-3.261.002-4.29.14m-.287 2.63a.75.75 0 0 1 1.056-.096L8.64 9.223c.933.777 1.58 1.315 2.128 1.667c.529.34.888.455 1.233.455c.345 0 .704-.114 1.233-.455c.547-.352 1.195-.89 2.128-1.667l2.159-1.8a.75.75 0 1 1 .96 1.153l-2.196 1.83c-.887.74-1.605 1.338-2.24 1.746c-.66.425-1.303.693-2.044.693c-.741 0-1.384-.269-2.045-.693c-.634-.408-1.352-1.007-2.239-1.745L5.52 8.577a.75.75 0 0 1-.096-1.057"
      clip-rule="evenodd"/>
  </symbol>
  <symbol id="instagram" viewBox="0 0 256 256">
    <path fill="currentColor"
      d="M128 80a48 48 0 1 0 48 48a48.05 48.05 0 0 0-48-48Zm0 80a32 32 0 1 1 32-32a32 32 0 0 1-32 32Zm48-136H80a56.06 56.06 0 0 0-56 56v96a56.06 56.06 0 0 0 56 56h96a56.06 56.06 0 0 0 56-56V80a56.06 56.06 0 0 0-56-56Zm40 152a40 40 0 0 1-40 40H80a40 40 0 0 1-40-40V80a40 40 0 0 1 40-40h96a40 40 0 0 1 40 40ZM192 76a12 12 0 1 1-12-12a12 12 0 0 1 12 12Z"/>
  </symbol>
  <symbol id="linkedin" viewBox="0 0 512 512">
    <path fill="currentColor"
      d="M444.17 32H70.28C49.85 32 32 46.7 32 66.89v374.72C32 461.91 49.85 480 70.28 480h373.78c20.54 0 35.94-18.21 35.94-38.39V66.89C480.12 46.7 464.6 32 444.17 32Zm-273.3 373.43h-64.18V205.88h64.18ZM141 175.54h-.46c-20.54 0-33.84-15.29-33.84-34.43c0-19.49 13.65-34.42 34.65-34.42s33.85 14.82 34.31 34.42c-.01 19.14-13.31 34.43-34.66 34.43Zm264.43 229.89h-64.18V296.32c0-26.14-9.34-44-32.56-44c-17.74 0-28.24 12-32.91 23.69c-1.75 4.2-2.22 9.92-2.22 15.76v113.66h-64.18V205.88h64.18v27.77c9.34-13.3 23.93-32.44 57.88-32.44c42.13 0 74 27.77 74 87.64Z"/>
  </symbol>
  <symbol id="twitter" viewBox="0 0 24 24">
    <path fill="currentColor"
      d="M22.46 6c-.77.35-1.6.58-2.46.69c.88-.53 1.56-1.37 1.88-2.38c-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29c0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15c0 1.49.75 2.81 1.91 3.56c-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07a4.28 4.28 0 0 0 4 2.98a8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21C16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56c.84-.6 1.56-1.36 2.14-2.23Z"/>
  </symbol>
  <symbol id="search" viewBox="0 0 24 24">
    <path fill="currentColor" fill-rule="evenodd"
      d="M11.5 2.75a8.75 8.75 0 1 0 0 17.5a8.75 8.75 0 0 0 0-17.5M1.25 11.5c0-5.66 4.59-10.25 10.25-10.25S21.75 5.84 21.75 11.5c0 2.56-.939 4.902-2.491 6.698l3.271 3.272a.75.75 0 1 1-1.06 1.06l-3.272-3.271A10.21 10.21 0 0 1 11.5 21.75c-5.66 0-10.25-4.59-10.25-10.25"
      clip-rule="evenodd"/>
  </symbol>
  <symbol id="arrow-right" viewBox="0 0 24 24">
    <path fill="currentColor" fill-rule="evenodd"
      d="M13.47 5.47a.75.75 0 0 1 1.06 0l6 6a.75.75 0 0 1 0 1.06l-6 6a.75.75 0 1 1-1.06-1.06l4.72-4.72H4a.75.75 0 0 1 0-1.5h14.19l-4.72-4.72a.75.75 0 0 1 0-1.06"
      clip-rule="evenodd"/>
  </symbol>
  <symbol id="arrow-left" viewBox="0 0 24 24">
    <path fill="currentColor" fill-rule="evenodd"
      d="M10.53 5.47a.75.75 0 0 1 0 1.06l-4.72 4.72H20a.75.75 0 0 1 0 1.5H5.81l4.72 4.72a.75.75 0 1 1-1.06 1.06l-6-6a.75.75 0 0 1 0-1.06l6-6a.75.75 0 0 1 1.06 0"
      clip-rule="evenodd"/>
  </symbol>
  <symbol id="navbar-icon" viewBox="0 0 16 16">
    <path d="M14 10.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 .5-.5zm0-3a.5.5 0 0 0-.5-.5h-7a.5.5 0 0 0 0 1h7a.5.5 0 0 0 .5-.5zm0-3a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0 0 1h11a.5.5 0 0 0 .5-.5z"/>
  </symbol>
</svg>

<!-- Preloader -->
<div class="preloader" id="preloader">
    <div class="loader"></div>
</div>

<header id="header">

    <!-- Top Info Bar -->
    <nav class="header-top py-1" style="border-bottom:1px solid rgba(0,0,0,.06);">
        <div class="container-fluid padding-side">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <ul class="info d-flex flex-wrap list-unstyled m-0" style="gap:0 20px;">
                    <li class="d-flex align-items-center" style="font-size:13px;">
                        <svg class="color me-1" width="14" height="14"><use xlink:href="#location"></use></svg>
                        Dehrakhas, Patel Nagar, Dehradun
                    </li>
                    <li class="d-flex align-items-center" style="font-size:13px;">
                        <svg class="color me-1" width="14" height="14"><use xlink:href="#phone"></use></svg>
                        <a href="tel:9634579408" style="color:inherit;">9634579408</a>
                    </li>
                    <li class="d-flex align-items-center d-none d-md-flex" style="font-size:13px;">
                        <svg class="color me-1" width="14" height="14"><use xlink:href="#email"></use></svg>
                        <a href="mailto:info@neoranewbi.in" style="color:inherit;">info@neoranewbi.in</a>
                    </li>
                </ul>
                <ul class="d-flex flex-wrap list-unstyled m-0 align-items-center" style="gap:0 14px;">
                    <li>
                        <a href="https://www.instagram.com/neora_newbies/" target="_blank" rel="noopener" aria-label="Instagram">
                            <svg class="social" width="15" height="15"><use xlink:href="#instagram"></use></svg>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.linkedin.com/company/neoranewbies/" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <svg class="social" width="15" height="15"><use xlink:href="#linkedin"></use></svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Primary Navbar -->
    <nav id="primary-header" class="navbar navbar-expand-lg py-3">
        <div class="container-fluid padding-side">
            <div class="d-flex justify-content-between align-items-center w-100">

                <!-- Logo -->
                <a class="navbar-brand" href="{{ $baseUrl }}">
                    <img
                        src="{{ landing_asset('landing/logo/logo.webp') }}"
                        alt="NEORA Logo"
                        class="logo img-fluid"
                    />
                </a>

                <!-- Mobile Sign In button (shown only on small screens, beside hamburger) -->
                <a
                    href="{{ route('login') }}"
                    class="d-flex d-lg-none order-2 align-items-center gap-1"
                    style="font-size:.78rem;font-weight:700;color:#fff;background:var(--primary-color,#c8a96e);padding:6px 14px;border-radius:50px;white-space:nowrap;text-decoration:none;line-height:1;"
                >
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Sign In
                </a>

                <!-- Mobile toggle (offcanvas) -->
                <button
                    class="navbar-toggler border-0 d-flex d-lg-none order-3 p-2 shadow-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#bdNavbar"
                    aria-controls="bdNavbar"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <svg class="navbar-icon" width="28" height="28"><use xlink:href="#navbar-icon"></use></svg>
                </button>

                <!-- Offcanvas nav (mobile) -->
                <div class="header-bottom offcanvas offcanvas-end" id="bdNavbar" aria-labelledby="bdNavbarOffcanvasLabel">
                    <div class="offcanvas-header px-4 pb-0">
                        <img src="{{ landing_asset('landing/logo/logo.webp') }}" alt="NEORA" style="height:40px;">
                        <button type="button" class="btn-close mt-2" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#bdNavbar"></button>
                    </div>
                    <div class="offcanvas-body align-items-center justify-content-center">
                        <ul class="navbar-nav align-items-center mb-2 mb-lg-0">
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                            </li>
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">Services</a>
                            </li>
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 nav-featured {{ request()->routeIs('resources.*') ? 'active' : '' }}" href="{{ route('resources.index') }}">Resources <span class="nav-featured-tag">New</span></a>
                            </li>
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Gallery</a>
                            </li>
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 {{ request()->routeIs('testimonials') ? 'active' : '' }}" href="{{ route('testimonials') }}">Testimonials</a>
                            </li>
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                            </li>
                            <li class="nav-item px-3">
                                <a class="nav-link p-0 nav-featured {{ request()->routeIs('careers') ? 'active' : '' }}" href="{{ route('careers') }}">Careers</a>
                            </li>
                        </ul>
                        <!-- Mobile Book Session -->
                        <div class="d-flex d-lg-none justify-content-center mt-4 px-3">
                            <a href="{{ route('contact') }}" class="nd-btn-pill-nav">Book Session</a>
                        </div>
                    </div>
                </div>

                <!-- Desktop right: search + CTA -->
                <div class="d-none d-lg-flex align-items-center gap-3">
                    <form class="position-relative" style="width:200px;">
                        <input
                            type="text"
                            class="form-control bg-secondary border-0 rounded-5 px-4 py-2"
                            style="font-size:.85rem;"
                            placeholder="Search..."
                        >
                        <a href="#" class="position-absolute top-50 end-0 translate-middle-y p-1 me-2">
                            <svg width="17" height="17"><use xlink:href="#search"></use></svg>
                        </a>
                    </form>
                    <a href="{{ route('login') }}" style="font-size:.85rem;font-weight:600;color:#555;white-space:nowrap;">Sign In</a>
                    <a href="{{ route('contact') }}" class="nd-btn-pill-nav">Book Session</a>
                </div>

            </div>
        </div>
    </nav>

</header>

<script>
(function () {
    // Preloader hide on page load
    function hidePreloader() {
        var preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.opacity    = '0';
            preloader.style.transition = 'opacity .5s';
            setTimeout(function () { preloader.style.display = 'none'; }, 520);
        }
    }
    if (document.readyState === 'complete') {
        hidePreloader();
    } else {
        window.addEventListener('load', hidePreloader);
    }

    // Sticky navbar scroll shadow
    document.addEventListener('DOMContentLoaded', function () {
        var header = document.getElementById('header');
        if (!header) return;
        window.addEventListener('scroll', function () {
            header.style.boxShadow = window.scrollY > 50
                ? '0 4px 24px rgba(0,0,0,.14)'
                : '0 2px 16px rgba(0,0,0,.07)';
        });
    });
})();
</script>
