<?php
// Hero Section — Full-width Swiper carousel with overlay content
$heroSlides = [
    [
        'img'     => 'public/landing/sliders/1.webp',
        'heading' => 'Helping You <span class="nd-accent">Communicate</span> Better',
        'sub'     => 'Your trusted partner for speech therapy, language development, and hearing health — personalised care for every stage of life.',
        'pos'     => 'center center',
    ],
    [
        'img'     => 'public/landing/sliders/2.webp',
        'heading' => 'Expert <span class="nd-accent">Audiology</span> Services',
        'sub'     => 'Comprehensive hearing evaluations, hearing-aid fittings, and auditory rehabilitation — tailored to your unique needs.',
        'pos'     => 'center 30%',
    ],
    [
        'img'     => 'public/landing/sliders/4.webp',
        'heading' => 'Personalised <span class="nd-accent">Therapy</span> for All Ages',
        'sub'     => 'Evidence-based, goal-oriented, and family-inclusive therapy sessions — helping every patient reach their full potential.',
        'pos'     => 'center center',
    ],
    [
        'img'     => 'public/landing/sliders/3.webp',
        'heading' => 'Your Journey to <span class="nd-accent">Better Health</span> Starts Here',
        'sub'     => 'Speech therapy, occupational therapy, special education, and early intervention — holistic care under one roof.',
        'pos'     => 'center 40%',
    ],
];
?>

<section id="hero-section">
    <div class="hero-swiper-wrap">

        <!-- Swiper carousel -->
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($heroSlides as $i => $slide): ?>
                <div class="swiper-slide">
                    <img
                        src="<?php echo htmlspecialchars($slide['img']); ?>"
                        alt="NEORA Clinic slide <?php echo $i + 1; ?>"
                        loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>"
                        style="object-position:<?php echo htmlspecialchars($slide['pos']); ?>;"
                    />
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation arrows -->
            <div class="swiper-button-prev" aria-label="Previous slide"></div>
            <div class="swiper-button-next" aria-label="Next slide"></div>

            <!-- Pagination dots -->
            <div class="swiper-pagination"></div>
        </div>

        <!-- Text overlay (left side) — updates with each slide -->
        <div class="hero-overlay">
            <div class="hero-overlay-inner">
                <p class="hero-eyebrow">Speech &amp; Audiology Clinic</p>
                <h1 class="hero-title" id="heroTitle"><?php echo $heroSlides[0]['heading']; ?></h1>
                <p class="hero-sub" id="heroSub"><?php echo htmlspecialchars($heroSlides[0]['sub']); ?></p>
                <a href="<?php echo BASE_URL; ?>contact.php#book" class="btn btn-arrow btn-primary">
                    <span>Book a Session
                        <svg width="18" height="18"><use xlink:href="#arrow-right"></use></svg>
                    </span>
                </a>
            </div>
        </div>

        <!-- Book Session card (right panel — desktop only) -->
        <div class="hero-form-panel">
            <div class="hero-form-card">
                <h3>Book a Session</h3>

                <label class="hero-form-label">Your Name</label>
                <input
                    type="text"
                    class="hero-form-input"
                    placeholder="Enter your name"
                    id="heroName"
                />

                <label class="hero-form-label">Phone / WhatsApp</label>
                <input
                    type="tel"
                    class="hero-form-input"
                    placeholder="+91 XXXXX XXXXX"
                    id="heroPhone"
                />

                <label class="hero-form-label">Service Needed</label>
                <select class="hero-form-input" id="heroService" style="appearance:auto;cursor:pointer;">
                    <option value="" disabled selected>Select a service</option>
                    <option>Speech Therapy</option>
                    <option>Audiology</option>
                    <option>Language Therapy</option>
                    <option>Occupational Therapy</option>
                    <option>Special Education</option>
                    <option>Early Intervention</option>
                    <option>Consult</option>
                </select>

                <div id="heroFormMsg" style="display:none;padding:10px 14px;border-radius:10px;font-size:.8rem;margin-top:4px;"></div>

                <div class="d-grid">
                    <button
                        onclick="ndHeroBooking()"
                        class="btn btn-arrow btn-primary mt-1"
                        type="button"
                        id="heroSubmitBtn"
                    >
                        <span id="heroSubmitBtnText">Request Appointment
                            <svg width="18" height="18"><use xlink:href="#arrow-right"></use></svg>
                        </span>
                    </button>
                </div>

                <!-- Stat pill -->
                <div style="display:flex;align-items:center;gap:12px;margin-top:20px;padding-top:18px;border-top:1px solid #f0f0f0;">
                    <div style="background:var(--green-color);color:#fff;border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;flex-shrink:0;font-family:var(--body-font);">500+</div>
                    <span style="font-size:.82rem;color:#777;font-family:var(--body-font);line-height:1.4;">Patients helped across<br>all age groups</span>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
(function () {
    var heroTitles = <?php echo json_encode(array_column($heroSlides, 'heading')); ?>;
    var heroSubs   = <?php echo json_encode(array_column($heroSlides, 'sub')); ?>;
    var titleEl    = document.getElementById('heroTitle');
    var subEl      = document.getElementById('heroSub');

    // Swiper hero init
    var heroSwiper = new Swiper('.hero-swiper', {
        loop:      true,
        effect:    'fade',
        autoplay:  { delay: 5000, disableOnInteraction: false },
        speed:     900,
        pagination: {
            el: '.hero-swiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.hero-swiper .swiper-button-next',
            prevEl: '.hero-swiper .swiper-button-prev',
        },
        on: {
            slideChangeTransitionStart: function () {
                if (titleEl) { titleEl.style.opacity = '0'; titleEl.style.transform = 'translateY(12px)'; }
                if (subEl)   { subEl.style.opacity   = '0'; subEl.style.transform   = 'translateY(8px)'; }
            },
            slideChangeTransitionEnd: function () {
                var idx = heroSwiper.realIndex;
                if (titleEl) {
                    titleEl.innerHTML = heroTitles[idx] || heroTitles[0];
                    titleEl.style.transition = 'opacity .6s, transform .6s';
                    titleEl.style.opacity    = '1';
                    titleEl.style.transform  = 'translateY(0)';
                }
                if (subEl) {
                    subEl.textContent        = heroSubs[idx] || heroSubs[0];
                    subEl.style.transition   = 'opacity .6s .1s, transform .6s .1s';
                    subEl.style.opacity      = '1';
                    subEl.style.transform    = 'translateY(0)';
                }
            },
        },
    });

    // Hero booking — submit to DB with duplicate check
    window.ndHeroBooking = function () {
        var name    = document.getElementById('heroName').value.trim();
        var phone   = document.getElementById('heroPhone').value.trim();
        var service = document.getElementById('heroService').value;
        var msgEl   = document.getElementById('heroFormMsg');
        var btn     = document.getElementById('heroSubmitBtn');
        var btnText = document.getElementById('heroSubmitBtnText');

        function showInlineErr(text) {
            msgEl.innerHTML  = text;
            msgEl.style.cssText = 'display:block;padding:10px 14px;border-radius:10px;font-size:.8rem;margin-top:4px;background:#f8d7da;color:#58151c;border:1px solid #f1aeb5;';
        }

        if (!name || !phone) {
            showInlineErr('Please enter your name and phone number.');
            return;
        }

        msgEl.style.display = 'none';
        btn.disabled = true;
        btnText.textContent = 'Submitting…';

        var fd = new FormData();
        fd.append('name',    name);
        fd.append('phone',   phone);
        fd.append('service', service || '');

        fetch('<?php echo BASE_URL; ?>api/book_session.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(res){
                if (res.success) {
                    document.getElementById('heroName').value    = '';
                    document.getElementById('heroPhone').value   = '';
                    document.getElementById('heroService').value = '';
                    if (typeof ndShowBookingPopup === 'function') ndShowBookingPopup('success', name);
                } else if (res.duplicate) {
                    if (typeof ndShowBookingPopup === 'function') ndShowBookingPopup('duplicate', name);
                } else {
                    if (typeof ndShowBookingPopup === 'function') ndShowBookingPopup('error', name);
                }
            })
            .catch(function(){
                if (typeof ndShowBookingPopup === 'function') ndShowBookingPopup('error', '');
            })
            .finally(function(){
                btn.disabled = false;
                btnText.innerHTML = 'Request Appointment <svg width="18" height="18"><use xlink:href="#arrow-right"></use></svg>';
            });
    };
})();
}); // end DOMContentLoaded
</script>
