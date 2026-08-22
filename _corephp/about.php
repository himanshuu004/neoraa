<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'About Us';
$pageDesc  = 'Learn about NEORA Therapy & Audiology Clinic — our mission, values, and the passionate team led by BASLP Priyanka Rawat.';
include __DIR__ . '/includes/landing/page_head.php';
?>

<?php include __DIR__ . '/includes/landing/navbar.php'; ?>

<!-- ── Page Hero ─────────────────────────────────────── -->
<div class="nd-page-hero" data-aos="fade-in">
    <div class="nd-page-hero-bg" style="background-image:url('public/landing/images/005c7ff0-5885-426a-9a03-f846973c7987.JPG');"></div>
    <div class="nd-page-hero-overlay"></div>
    <div class="nd-page-hero-content">
        <div class="container-fluid px-4 px-lg-5">
            <div class="nd-breadcrumb">
                <a href="<?php echo BASE_URL; ?>">Home</a>
                <span>›</span>
                <span style="color:rgba(255,255,255,.75);">About Us</span>
            </div>
            <p class="nd-page-hero-eyebrow">Our Story</p>
            <h1 class="nd-page-hero-title">About <span>NEORA</span></h1>
            <p class="nd-page-hero-sub">Committed to helping every individual communicate, hear, and live better.</p>
        </div>
    </div>
</div>

<!-- ── About Us ─────────────────────────────────────── -->
<section class="nd-section bg-white" id="about">
    <div class="nd-section-inner">
        <div class="nd-two-col" style="position:relative;">

            <div data-aos="fade-right">
                <span class="nd-section-label">About Us</span>
                <h2 class="nd-section-title">
                    We Help You <span class="nd-accent">Communicate</span> &amp; Hear Better
                </h2>
                <p class="nd-section-text">
                    Welcome to <strong>NEORA</strong>, a leading speech therapy and audiology clinic
                    dedicated to helping individuals of all ages improve their communication and hearing abilities.
                    We provide comprehensive therapeutic services including Speech Therapy, Audiology, Language Therapy,
                    Occupational Therapy, Special Education, and Early Intervention.
                </p>
                <p class="nd-section-text" style="margin-top:-16px;">
                    Our team of experienced professionals is committed to providing personalised care and evidence-based
                    treatments. We believe in a holistic, individual-centred, goal-oriented, and family-inclusive approach
                    to therapy — helping you achieve your communication goals and improve your quality of life.
                </p>
                <a href="<?php echo BASE_URL; ?>contact.php" class="nd-btn nd-btn-orange">
                    Book a Consultation
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <i style="position:absolute;bottom:-30px;left:-20px;font-size:2.5rem;color:#e0e0e0;user-select:none;pointer-events:none;font-style:normal;">✦</i>
            </div>

            <div data-aos="fade-left" data-aos-delay="150">
                <div class="nd-img-card">
                    <img src="public/landing/images/005c7ff0-5885-426a-9a03-f846973c7987.JPG" alt="NEORA Clinic interior"/>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ── Our Mission & Values ──────────────────────────── -->
<section class="nd-section bg-gray-50" id="mission">
    <div class="nd-section-inner">
        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" data-aos="fade-up" style="margin-bottom:48px;">
            <span class="nd-section-label">Our Foundation</span>
            <h2 class="nd-section-title">Mission, Vision &amp; <span class="nd-accent">Values</span></h2>
        </div>

        <div class="row g-4" style="padding:0 1rem;" data-aos="fade-up" data-aos-delay="100">
            <!-- Mission -->
            <div class="col-md-4">
                <div style="background:#fff;border-radius:20px;padding:36px 28px;height:100%;box-shadow:0 4px 20px rgba(0,0,0,.06);border-top:4px solid var(--primary-color);">
                    <div style="width:52px;height:52px;border-radius:14px;background:rgba(223,85,137,.10);display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <svg width="26" height="26" fill="none" stroke="var(--primary-color)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 style="font-family:var(--heading-font);font-size:1.55rem;font-weight:400;color:var(--black-color);margin-bottom:12px;">Our Mission</h3>
                    <p style="color:var(--gray-color);font-size:.9rem;line-height:1.75;margin:0;">
                        To provide personalised, evidence-based therapy that empowers every individual — child or adult — to communicate confidently, hear clearly, and live fully.
                    </p>
                </div>
            </div>
            <!-- Vision -->
            <div class="col-md-4">
                <div style="background:#fff;border-radius:20px;padding:36px 28px;height:100%;box-shadow:0 4px 20px rgba(0,0,0,.06);border-top:4px solid var(--green-color);">
                    <div style="width:52px;height:52px;border-radius:14px;background:rgba(161,196,74,.12);display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <svg width="26" height="26" fill="none" stroke="var(--green-color)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 style="font-family:var(--heading-font);font-size:1.55rem;font-weight:400;color:var(--black-color);margin-bottom:12px;">Our Vision</h3>
                    <p style="color:var(--gray-color);font-size:.9rem;line-height:1.75;margin:0;">
                        To be the most trusted and inclusive therapy centre in Uttarakhand — where every patient feels heard, valued, and supported throughout their journey.
                    </p>
                </div>
            </div>
            <!-- Values -->
            <div class="col-md-4">
                <div style="background:#fff;border-radius:20px;padding:36px 28px;height:100%;box-shadow:0 4px 20px rgba(0,0,0,.06);border-top:4px solid #00A896;">
                    <div style="width:52px;height:52px;border-radius:14px;background:rgba(0,168,150,.10);display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <svg width="26" height="26" fill="none" stroke="#00A896" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 style="font-family:var(--heading-font);font-size:1.55rem;font-weight:400;color:var(--black-color);margin-bottom:12px;">Our Values</h3>
                    <p style="color:var(--gray-color);font-size:.9rem;line-height:1.75;margin:0;">
                        Compassion, integrity, and excellence. We are holistic, goal-oriented, and family-inclusive in everything we do — putting the patient at the heart of every decision.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ── Meet Our Director ─────────────────────────────── -->
<section class="nd-section bg-white" id="director">
    <div class="nd-section-inner">
        <div class="nd-two-col" style="position:relative;">

            <div data-aos="fade-right">
                <div class="nd-director-card">
                    <img src="public/landing/Director/Director.png" alt="BASLP Priyanka Rawat — Director &amp; Lead Therapist"/>
                    <div class="nd-director-card-overlay">
                        <div class="nd-director-card-name">BASLP Priyanka Rawat</div>
                        <div class="nd-director-card-title">Director &amp; Lead Therapist · NEORA</div>
                    </div>
                </div>
            </div>

            <div data-aos="fade-left" data-aos-delay="150" style="position:relative;">
                <i style="position:absolute;top:-20px;right:-10px;font-size:2rem;color:var(--green-color);opacity:.25;user-select:none;font-style:normal;">✦</i>

                <span class="nd-section-label">Meet Our Director</span>
                <h2 class="nd-section-title">BASLP <span class="nd-accent">Priyanka</span> Rawat</h2>
                <p style="color:var(--primary-color);font-weight:700;font-size:.9rem;margin-bottom:4px;">
                    Founder – NEORA Therapy &amp; Audiology Clinic
                </p>
                <p style="color:var(--green-color);font-weight:600;font-size:.9rem;margin-bottom:20px;">
                    Director &amp; Lead Therapist
                </p>
                <p class="nd-section-text">
                    BASLP Priyanka Rawat is the Director and Lead Therapist at NEORA. She is a qualified
                    and dedicated professional with a strong passion for helping individuals improve their
                    communication, learning, and daily life skills.
                </p>
                <p class="nd-section-text" style="margin-top:-16px;">
                    She works with children and adults and believes that therapy should be personalised,
                    ethical, and focused on real progress — not just sessions. Her approach is holistic,
                    individual-centred, goal-oriented, and family-inclusive.
                </p>
                <div class="nd-badge-row">
                    <span class="nd-badge">BASLP Certified</span>
                    <span class="nd-badge">Speech &amp; Language Therapy</span>
                    <span class="nd-badge">Audiology Specialist</span>
                </div>
                <a href="<?php echo BASE_URL; ?>contact.php" class="nd-btn nd-btn-orange">
                    Book a Session
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- ── Why Choose NEORA ──────────────────────────────── -->
<section class="nd-section bg-gray-50" id="why-us">
    <div class="nd-section-inner">
        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" data-aos="fade-up" style="margin-bottom:48px;">
            <span class="nd-section-label">Why Us</span>
            <h2 class="nd-section-title">Why Families Choose <span class="nd-accent">NEORA</span></h2>
        </div>

        <div class="row g-4" style="padding:0 1rem;">
            <?php
            $reasons = [
                ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title'=>'BASLP Certified', 'desc'=>'All therapy is delivered by a fully qualified, certified professional.', 'color'=>'var(--primary-color)'],
                ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title'=>'Family-Inclusive', 'desc'=>'We actively involve families in every step of the therapy journey.', 'color'=>'var(--green-color)'],
                ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title'=>'Evidence-Based', 'desc'=>'Our protocols are grounded in the latest research and best practices.', 'color'=>'#00A896'],
                ['icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'title'=>'Personalised Care', 'desc'=>'Every plan is tailored to the unique needs and goals of each individual.', 'color'=>'var(--primary-color)'],
                ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title'=>'Flexible Scheduling', 'desc'=>'Convenient appointment times to suit children, adults, and working families.', 'color'=>'var(--green-color)'],
                ['icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'title'=>'Holistic Approach', 'desc'=>'Speech, hearing, occupational therapy, and special education under one roof.', 'color'=>'#00A896'],
            ];
            ?>
            <?php foreach ($reasons as $i => $r): ?>
            <div class="col-md-4 col-6" data-aos="fade-up" data-aos-delay="<?php echo ($i % 3) * 80; ?>">
                <div style="display:flex;align-items:flex-start;gap:16px;padding:20px 0;">
                    <div style="flex-shrink:0;width:44px;height:44px;border-radius:12px;background:<?php echo $r['color']; ?>1a;display:flex;align-items:center;justify-content:center;">
                        <svg width="22" height="22" fill="none" stroke="<?php echo $r['color']; ?>" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?php echo $r['icon']; ?>"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.9rem;color:var(--black-color);margin-bottom:4px;"><?php echo $r['title']; ?></div>
                        <div style="font-size:.82rem;color:var(--gray-color);line-height:1.6;"><?php echo $r['desc']; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php include __DIR__ . '/includes/landing/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });
});
</script>
</body>
</html>
