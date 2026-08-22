<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery – NEORA Speech Therapy &amp; Audiology Clinic</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Google Fonts: same as main page -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <!-- Swiper (lightbox carousel) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Main design system -->
    <link rel="stylesheet" href="{{ $baseUrl }}assets/css/neora-redesign.css">

    <style>
        /* ── Gallery page ────────────────────────── */

        /* hero banner */
        .nd-gallery-hero {
            position: relative;
            height: 360px;
            overflow: hidden;
            display: flex; align-items: flex-end;
        }
        @media (max-width: 767px) { .nd-gallery-hero { height: 220px; } }
        @media (max-width: 480px) { .nd-gallery-hero { height: 180px; } }
        .nd-gallery-hero-bg {
            position: absolute; inset: 0;
            background: url('{{ landing_asset('landing/sliders/2296b393-0309-42ba-b6cc-405569f55368.JPG') }}') center 40% / cover no-repeat;
            transition: transform 8s ease;
        }
        .nd-gallery-hero:hover .nd-gallery-hero-bg { transform: scale(1.03); }
        .nd-gallery-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top,
                rgba(26,26,46,.85) 0%,
                rgba(223,85,137,.30) 60%,
                rgba(0,0,0,.20) 100%);
        }
        .nd-gallery-hero-content {
            position: relative; z-index: 2;
            padding: 0 0 52px 0;
            width: 100%;
        }

        /* section wrapper */
        .nd-gallery-section {
            padding: 80px 0 100px;
            background: var(--secondary-color);
        }
        @media (max-width: 767px) { .nd-gallery-section { padding: 48px 0 60px; } }
        @media (max-width: 480px) { .nd-gallery-section { padding: 32px 0 40px; } }

        /* filter tabs */
        .nd-gallery-filters {
            display: flex; flex-wrap: wrap; gap: 10px;
            justify-content: center;
            margin-bottom: 48px;
        }
        .nd-filter-btn {
            padding: 8px 22px;
            border-radius: 100px;
            border: 1.5px solid #ddd;
            background: #fff;
            font-family: var(--body-font); font-size: .82rem; font-weight: 600;
            color: #555; cursor: pointer;
            transition: all .22s;
        }
        .nd-filter-btn:hover,
        .nd-filter-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 14px rgba(223,85,137,.30);
        }

        /* masonry / grid */
        .nd-gallery-grid {
            columns: 4 280px;
            column-gap: 20px;
        }
        @media (max-width: 991px) { .nd-gallery-grid { columns: 3; column-gap: 14px; } }
        @media (max-width: 767px) { .nd-gallery-grid { columns: 2; column-gap: 10px; } }
        @media (max-width: 480px) { .nd-gallery-grid { columns: 2; column-gap: 8px; } }

        .nd-gallery-item {
            break-inside: avoid;
            margin-bottom: 20px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            box-shadow: 0 4px 18px rgba(0,0,0,.10);
            transition: transform .3s ease, box-shadow .3s ease;
        }
        @media (max-width: 767px) { .nd-gallery-item { margin-bottom: 10px; border-radius: 10px; } }
        @media (max-width: 480px) { .nd-gallery-item { margin-bottom: 8px;  border-radius: 8px; } }

        /* Cap image height so images stay small on mobile */
        @media (max-width: 767px) { .nd-gallery-item img { max-height: 200px; } }
        @media (max-width: 480px) { .nd-gallery-item img { max-height: 160px; } }
        .nd-gallery-item:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 14px 40px rgba(0,0,0,.18);
        }
        .nd-gallery-item img {
            width: 100%; display: block;
            object-fit: cover;
            transition: transform .5s ease;
        }
        .nd-gallery-item:hover img { transform: scale(1.06); }

        /* hover overlay */
        .nd-gallery-item-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top,
                rgba(223,85,137,.70) 0%,
                rgba(161,196,74,.20) 60%,
                transparent 100%);
            opacity: 0; transition: opacity .3s ease;
            display: flex; align-items: flex-end; padding: 16px;
        }
        .nd-gallery-item:hover .nd-gallery-item-overlay { opacity: 1; }
        .nd-gallery-item-zoom {
            background: rgba(255,255,255,.20);
            border: 1.5px solid rgba(255,255,255,.50);
            border-radius: 50%; width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; margin-left: auto;
            backdrop-filter: blur(4px);
            transition: background .2s;
        }
        .nd-gallery-item:hover .nd-gallery-item-zoom { background: rgba(255,255,255,.35); }

        /* ── Lightbox ─────────────────────────────── */
        .nd-lightbox-backdrop {
            display: none;
            position: fixed; inset: 0; z-index: 1055;
            background: rgba(10,10,18,.95);
            align-items: center; justify-content: center;
        }
        .nd-lightbox-backdrop.active { display: flex; }

        .nd-lightbox-inner {
            position: relative;
            width: 90vw; max-width: 960px;
        }
        .nd-lightbox-img {
            width: 100%; max-height: 82vh;
            object-fit: contain; border-radius: 12px;
            display: block;
        }
        .nd-lightbox-close {
            position: absolute; top: -44px; right: 0;
            background: none; border: none;
            color: rgba(255,255,255,.7); font-size: 1.6rem;
            cursor: pointer; padding: 4px 10px;
            transition: color .2s;
        }
        .nd-lightbox-close:hover { color: var(--primary-color); }
        .nd-lightbox-nav {
            position: absolute; top: 50%; transform: translateY(-50%);
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.25);
            backdrop-filter: blur(6px);
            color: #fff; width: 46px; height: 46px;
            border-radius: 50%; font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background .2s, color .2s;
        }
        .nd-lightbox-nav:hover { background: var(--primary-color); border-color: var(--primary-color); }
        .nd-lightbox-prev { left: -64px; }
        .nd-lightbox-next { right: -64px; }
        @media (max-width: 767px) {
            .nd-lightbox-prev { left: -44px; }
            .nd-lightbox-next { right: -44px; }
        }
        .nd-lightbox-counter {
            text-align: center; margin-top: 14px;
            font-size: .78rem; color: rgba(255,255,255,.45);
            font-family: var(--body-font);
        }
    </style>
</head>
<body>

@include('landing.partials.navbar')

<!-- ── Hero banner ──────────────────────────────────── -->
<div class="nd-gallery-hero" data-aos="fade-in">
    <div class="nd-gallery-hero-bg"></div>
    <div class="nd-gallery-hero-overlay"></div>
    <div class="nd-gallery-hero-content">
        <div class="container-fluid px-4 px-lg-5">
            <p style="font-size:.7rem;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,.55);margin-bottom:8px;">
                Our Space
            </p>
            <h1 style="font-family:var(--heading-font);font-size:clamp(2rem,5vw,3.4rem);font-weight:400;color:#fff;margin:0;line-height:1.1;">
                Clinic <span style="color:#ffcde0;">Gallery</span>
            </h1>
            <p style="color:rgba(255,255,255,.70);font-size:.9rem;margin-top:10px;max-width:480px;">
                A warm, professional environment designed to help every patient feel safe and supported.
            </p>
        </div>
    </div>
</div>

<!-- ── Gallery section ──────────────────────────────── -->
<section class="nd-gallery-section">
    <div class="container-fluid px-4 px-lg-5">

        <!-- heading -->
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="nd-section-label">Inside NEORA</span>
            <h2 class="nd-section-title" style="max-width:560px;margin-left:auto;margin-right:auto;">
                A Peek at Our <span class="nd-accent">Clinic</span>
            </h2>
            <p class="nd-section-text" style="max-width:520px;margin:0 auto;">
                Welcoming therapy rooms, state-of-the-art audiology equipment, and spaces built for comfort and focus.
            </p>
        </div>

        <!-- masonry grid -->
        <?php
        $galleryImages = [
            ['src' => landing_asset('landing/images/5bfe9bd1-6666-4281-8420-072ba3d40132.webp'), 'label' => 'Paediatric Play Area'],
            ['src' => landing_asset('landing/images/005c7ff0-5885-426a-9a03-f846973c7987.webp'), 'label' => 'Therapy Room'],
            ['src' => landing_asset('landing/images/46d06ad3-baa1-4ea7-bdcb-37cff039943c.webp'), 'label' => 'Consultation Room'],
            ['src' => landing_asset('landing/images/79ab4111-df96-4bd8-806d-929e963fc0b2.webp'), 'label' => 'Assessment Room'],
            ['src' => landing_asset('landing/images/494bd119-204d-4c5f-9b19-29fb0ae98677.webp'), 'label' => 'Reception & Welcome Area'],
            ['src' => landing_asset('landing/images/2296b393-0309-42ba-b6cc-405569f55368.webp'), 'label' => 'Audiology Suite'],
            ['src' => landing_asset('landing/images/d3263e16-976f-4a9f-8842-a2e3b75dadf3.JPG'), 'label' => 'Equipment & Tools'],
            ['src' => landing_asset('landing/images/e3adc225-888a-426e-9deb-70bc753adbc8.JPG'), 'label' => 'Waiting Lounge'],
            ['src' => landing_asset('landing/images/e57a6dc9-497e-44bd-9064-cef0b60fd708.JPG'), 'label' => 'NEORA Clinic'],
            ['src' => landing_asset('landing/images/IMG_2647.JPEG'),                             'label' => 'Clinic Environment'],
            ['src' => landing_asset('landing/images/IMG_2650 2.webp'),                           'label' => 'Team at Work'],
            ['src' => landing_asset('landing/images/IMG_2650.webp'),                             'label' => 'Session in Progress'],
        ];
        ?>

        <div class="nd-gallery-grid" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($galleryImages as $i => $img): ?>
            <div class="nd-gallery-item"
                 data-index="<?php echo $i; ?>"
                 data-aos="zoom-in"
                 data-aos-delay="<?php echo min(($i % 4) * 60, 200); ?>">
                <img
                    src="<?php echo htmlspecialchars($img['src']); ?>"
                    alt="<?php echo htmlspecialchars($img['label']); ?>"
                    loading="<?php echo $i < 4 ? 'eager' : 'lazy'; ?>"
                />
                <div class="nd-gallery-item-overlay">
                    <div class="nd-gallery-item-zoom">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 8v6M8 11h6"/>
                        </svg>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA -->
        <div class="text-center mt-5 pt-3" data-aos="fade-up" data-aos-delay="200">
            <a href="{{ $baseUrl }}" class="nd-btn nd-btn-orange">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:6px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</section>

<!-- ── Lightbox ─────────────────────────────────────── -->
<div class="nd-lightbox-backdrop" id="ndLightbox" role="dialog" aria-modal="true" aria-label="Image lightbox">
    <div class="nd-lightbox-inner">
        <button class="nd-lightbox-close" id="ndLbClose" aria-label="Close">&times;</button>
        <button class="nd-lightbox-nav nd-lightbox-prev" id="ndLbPrev" aria-label="Previous image">&#8592;</button>
        <img src="" alt="" class="nd-lightbox-img" id="ndLbImg">
        <button class="nd-lightbox-nav nd-lightbox-next" id="ndLbNext" aria-label="Next image">&#8594;</button>
        <p class="nd-lightbox-counter" id="ndLbCounter"></p>
    </div>
</div>

@include('landing.partials.footer')

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 900, easing: 'ease-in-out', once: true, offset: 60 });
    }

    // ── Lightbox logic ──────────────────────────
    var images = <?php
        echo json_encode(array_map(function($img) {
            return ['src' => $img['src'], 'label' => $img['label']];
        }, $galleryImages));
    ?>;

    var backdrop = document.getElementById('ndLightbox');
    var lbImg    = document.getElementById('ndLbImg');
    var lbCtr    = document.getElementById('ndLbCounter');
    var current  = 0;

    function openLightbox(idx) {
        current = idx;
        lbImg.src = images[current].src;
        lbImg.alt = images[current].label;
        lbCtr.textContent = (current + 1) + ' / ' + images.length;
        backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        backdrop.classList.remove('active');
        document.body.style.overflow = '';
        lbImg.src = '';
    }

    function prevImg() { current = (current - 1 + images.length) % images.length; openLightbox(current); }
    function nextImg() { current = (current + 1) % images.length; openLightbox(current); }

    // open on item click
    document.querySelectorAll('.nd-gallery-item').forEach(function (el) {
        el.addEventListener('click', function () { openLightbox(parseInt(this.dataset.index)); });
    });

    document.getElementById('ndLbClose').addEventListener('click', closeLightbox);
    document.getElementById('ndLbPrev').addEventListener('click', prevImg);
    document.getElementById('ndLbNext').addEventListener('click', nextImg);

    // click outside image closes
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeLightbox();
    });

    // keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (!backdrop.classList.contains('active')) return;
        if (e.key === 'ArrowLeft')  prevImg();
        if (e.key === 'ArrowRight') nextImg();
        if (e.key === 'Escape')     closeLightbox();
    });
});
</script>
</body>
</html>
