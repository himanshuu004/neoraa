<?php require_once __DIR__ . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NEORA - Leading speech therapy and audiology clinic in Dehradun. Expert care for speech, language, hearing, and occupational therapy.">
    <title>NEORA - Speech Therapy &amp; Audiology Clinic</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Google Fonts: Cormorant Upright (headings) + Sora (body) — Mellow design system -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@100..800&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- NEORA — Mellow-inspired design system -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL); ?>assets/css/neora-redesign.css">

    <!-- JS Libraries loaded in head for inline-script compatibility -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>

    <style>
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c8b89a; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8936c; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/landing/navbar.php'; ?>
<?php include __DIR__ . '/includes/landing/hero.php'; ?>


<!-- =====================================================================
     ABOUT US  →  Mission Section  (two-column: left text, right image)
     ===================================================================== -->
<?php
$section_title    = '';
$section_bg_color = 'bg-white';
$section_id       = 'about';
ob_start();
?>
<div class="nd-two-col" style="position:relative;">

    <!-- Left: text content -->
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
        <a href="<?php echo BASE_URL; ?>contact.php#book" class="nd-btn nd-btn-orange">
            Book a Consultation
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>

        <!-- Mobile-only compact booking form (hero form panel is hidden on phones) -->
        <div class="nd-mobile-book-wrap">
            <h4 class="nd-mobile-book-title">Book a Session</h4>
            <label class="hero-form-label">Your Name</label>
            <input type="text" id="mbName" class="hero-form-input" placeholder="Enter your name">
            <label class="hero-form-label">Phone / WhatsApp</label>
            <input type="tel" id="mbPhone" class="hero-form-input" placeholder="+91 XXXXX XXXXX">
            <label class="hero-form-label">Service Needed</label>
            <select id="mbService" class="hero-form-input" style="appearance:auto;cursor:pointer;">
                <option value="" disabled selected>Select a service</option>
                <option>Speech Therapy</option>
                <option>Audiology</option>
                <option>Language Therapy</option>
                <option>Occupational Therapy</option>
                <option>Special Education</option>
                <option>Early Intervention</option>
                <option>Consult</option>
            </select>
            <div id="mbMsg" style="display:none;padding:10px 14px;border-radius:10px;font-size:.8rem;margin-top:4px;"></div>
            <button onclick="ndMobileBooking()" class="nd-btn nd-btn-orange" style="width:100%;justify-content:center;margin-top:6px;" id="mbSubmitBtn" type="button">
                <span id="mbSubmitBtnText">
                    Request Appointment
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </button>
        </div>

        <!-- Decorative star -->
        <i style="position:absolute;bottom:-30px;left:-20px;font-size:2.5rem;color:#e0e0e0;user-select:none;pointer-events:none;font-style:normal;">✦</i>
    </div>

    <!-- Right: image card -->
    <div data-aos="fade-left" data-aos-delay="150">
        <div class="nd-img-card">
            <img
                src="public/landing/images/005c7ff0-5885-426a-9a03-f846973c7987.webp"
                alt="NEORA Clinic interior"
                onerror="this.src='public/landing/sliders/2.JPG'"
            />
        </div>
    </div>
</div>
<?php
$section_content = ob_get_clean();
include __DIR__ . '/includes/landing/section.php';
?>


<!-- =====================================================================
     MEET OUR DIRECTOR  →  Vision Section  (reversed: left image, right text)
     ===================================================================== -->
<?php
$section_title    = '';
$section_bg_color = 'bg-gray-50';
$section_id       = '';
ob_start();
?>
<div class="nd-two-col" style="position:relative;">

    <!-- Left: Director photo card -->
    <div data-aos="fade-right">
        <div class="nd-director-card">
            <img
                src="public/landing/Director/Director.webp"
                alt="BASLP Priyanka Rawat — Director &amp; Lead Therapist"
            />
            <!-- Name overlay at bottom of card -->
            <div class="nd-director-card-overlay">
                <div class="nd-director-card-name">BASLP Priyanka Rawat</div>
                <div class="nd-director-card-title">Director &amp; Lead Therapist · NEORA</div>
            </div>
        </div>
    </div>

    <!-- Right: director details -->
    <div data-aos="fade-left" data-aos-delay="150" style="position:relative;">
        <i style="position:absolute;top:-20px;right:-10px;font-size:2rem;color:var(--green-color);opacity:.25;user-select:none;font-style:normal;">✦</i>

        <span class="nd-section-label">Meet Our Director</span>
        <h2 class="nd-section-title">
            BASLP <span class="nd-accent">Priyanka</span> Rawat
        </h2>
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

        <a href="#about" class="nd-btn nd-btn-orange">
            Learn More
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</div>
<?php
$section_content = ob_get_clean();
include __DIR__ . '/includes/landing/section.php';
?>


<!-- =====================================================================
     GALLERY PREVIEW  →  Card Grid Section
     ===================================================================== -->
<?php
$section_title    = 'Our Clinic Gallery';
$section_bg_color = 'bg-white';
$section_id       = 'gallery';
ob_start();
?>
<p style="text-align:center;color:#777;margin-bottom:40px;font-size:1rem;" data-aos="fade-up">
    Take a look at our welcoming clinic environment and facilities
</p>

<div class="nd-cards-grid" style="margin-bottom:40px;">
    <?php
    $galleryImages = [
        'public/landing/images/5bfe9bd1-6666-4281-8420-072ba3d40132.webp',
        'public/landing/images/005c7ff0-5885-426a-9a03-f846973c7987.webp',
        'public/landing/images/46d06ad3-baa1-4ea7-bdcb-37cff039943c.webp',
        'public/landing/images/79ab4111-df96-4bd8-806d-929e963fc0b2.webp',
    ];
    foreach ($galleryImages as $index => $image):
    ?>
        <div class="nd-gallery-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 80; ?>">
            <img
                src="<?php echo htmlspecialchars($image); ?>"
                alt="Gallery preview <?php echo $index + 1; ?>"
                loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
            />
            <div class="nd-gallery-card-overlay"></div>
        </div>
    <?php endforeach; ?>
</div>

<div style="text-align:center;" data-aos="fade-up" data-aos-delay="320">
    <a href="<?php echo htmlspecialchars(BASE_URL . 'gallery.php'); ?>" class="nd-btn nd-btn-orange">
        Explore More
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </a>
</div>
<?php
$section_content = ob_get_clean();
include __DIR__ . '/includes/landing/section.php';
?>


<!-- =====================================================================
     OUR SERVICES  →  3/4-Column Coloured Feature Strip
     ===================================================================== -->
<?php
$section_title    = '';
$section_bg_color = 'bg-gray-50';
$section_id       = 'services';
ob_start();
$infographic_services = [
    [
        'title'   => 'Speech Therapy',
        'col'     => 'nd-feature-col--teal',
        'desc'    => 'Comprehensive speech and language assessment and therapy for children and adults — helping you find your voice.',
        'icon_d'  => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    ],
    [
        'title'   => 'Audiology Services',
        'col'     => 'nd-feature-col--yellow',
        'desc'    => 'Hearing evaluations, hearing-aid fittings, and auditory rehabilitation services for all age groups.',
        'icon_d'  => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3',
    ],
    [
        'title'   => 'Language Therapy',
        'col'     => 'nd-feature-col--red',
        'desc'    => 'Specialised language therapy for children and adults facing communication challenges or developmental delays.',
        'icon_d'  => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    ],
    [
        'title'   => 'Occupational Therapy',
        'col'     => 'nd-feature-col--navy',
        'desc'    => 'Supporting daily living skills, fine motor development, and sensory integration for meaningful independence.',
        'icon_d'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    ],
];
?>

<!-- Section heading above the strip -->
<div class="nd-heading-group nd-heading-group--center" style="margin-bottom:44px;" data-aos="fade-up">
    <span class="nd-section-label">What We Offer</span>
    <h2 class="nd-section-title">Our <span class="nd-accent">Services</span></h2>
    <p class="nd-section-text" style="margin-left:auto;margin-right:auto;max-width:560px;">
        From speech and language therapy to hearing evaluations and occupational support —
        personalised, evidence-based care for all ages.
    </p>
</div>

<!-- Coloured feature strip -->
<div class="nd-feature-strip" data-aos="fade-up" data-aos-delay="100">
    <?php foreach ($infographic_services as $s): ?>
        <div class="nd-feature-col <?php echo $s['col']; ?>">
            <svg class="nd-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?php echo $s['icon_d']; ?>"/>
            </svg>
            <h3 class="nd-feature-h3"><?php echo htmlspecialchars($s['title']); ?></h3>
            <p class="nd-feature-text"><?php echo htmlspecialchars($s['desc']); ?></p>
            <a href="#services" class="nd-feature-link">Learn More</a>
        </div>
    <?php endforeach; ?>
</div>

<div style="text-align:center;margin-top:40px;" data-aos="fade-up" data-aos-delay="200">
    <a href="<?php echo BASE_URL; ?>contact.php#book" class="nd-btn nd-btn-orange">Book a Session Today</a>
</div>
<?php
$section_content = ob_get_clean();
include __DIR__ . '/includes/landing/section.php';
?>


<!-- =====================================================================
     HOW OUR SESSIONS WORK  →  Process Steps Section
     ===================================================================== -->
<?php
$section_title    = 'How Our Sessions Work';
$section_bg_color = 'bg-white';
$section_id       = '';
ob_start();
$steps = [
    ['num' => 1, 'title' => 'Initial Assessment',  'desc' => 'Understanding your unique needs, communication goals, and medical background.'],
    ['num' => 2, 'title' => 'Treatment Plan',       'desc' => 'A personalised, evidence-based approach designed around you and your family.'],
    ['num' => 3, 'title' => 'Regular Sessions',     'desc' => 'Consistent therapy with real-time progress tracking and feedback.'],
    ['num' => 4, 'title' => 'Ongoing Support',      'desc' => 'Continuous care, follow-ups, and family guidance beyond the clinic.'],
];
?>
<div class="nd-steps-grid">
    <?php foreach ($steps as $index => $step): ?>
        <div class="nd-step-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
            <div class="nd-step-badge"><?php echo $step['num']; ?></div>
            <div class="nd-step-content">
                <div class="nd-step-title"><?php echo htmlspecialchars($step['title']); ?></div>
                <div class="nd-step-text"><?php echo htmlspecialchars($step['desc']); ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div style="text-align:center;margin-top:40px;" data-aos="fade-up" data-aos-delay="400">
    <a href="<?php echo BASE_URL; ?>contact.php#book" class="nd-btn nd-btn-orange">Learn More About Sessions</a>
</div>
<?php
$section_content = ob_get_clean();
include __DIR__ . '/includes/landing/section.php';
?>


<!-- =====================================================================
     REVIEWS FROM FAMILIES  →  Testimonial Cards + Submission Form
     ===================================================================== -->
<?php
$section_title    = 'Reviews from Families';
$section_bg_color = 'bg-gray-50';
$section_id       = 'testimonials';
ob_start();
$testimonials = [];
try {
    require_once __DIR__ . '/config/reviews_schema.php';
    $stmt = $pdo->query("SELECT id, `text`, author, `location`, photo_path FROM reviews ORDER BY display_order ASC, id ASC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // keep empty; section still renders
}

$totalReviews  = count($testimonials);
$hasMoreReviews = $totalReviews > 6;
$remainingCount = max(0, $totalReviews - 6);
$avatarColors   = ['#B45309','#0D9488','#475569','#B91C1C','#4338CA','#059669'];
$displayedReviews = array_slice($testimonials, 0, 6);
?>

<!-- Write a Review — prominent button BEFORE reviews -->
<div style="text-align:center;margin-bottom:36px;" data-aos="fade-up">
    <button type="button" id="reviewFormToggle" class="nd-write-review-styled">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Write a Review
    </button>
</div>

<!-- Collapsible review submission form -->
<div id="reviewFormContent" class="nd-review-form-wrap hidden" style="max-width:680px;margin:0 auto 48px;" data-aos="fade-up">
    <div class="nd-form-inner">
        <form id="reviewForm" enctype="multipart/form-data">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label for="author" class="nd-form-label">Your Name <span style="color:#E84855;">*</span></label>
                    <input type="text" id="author" name="author" required class="nd-form-input" placeholder="Enter your name">
                </div>
                <div>
                    <label for="photo" class="nd-form-label">
                        Profile Photo <span style="font-weight:400;color:#aaa;">(optional)</span>
                    </label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/jpg,image/png" class="nd-form-input" style="padding:10px 12px;cursor:pointer;">
                        <div id="photoPreview" class="hidden">
                            <img id="previewImg" src="" alt="Preview" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e0e0e0;">
                        </div>
                    </div>
                    <p style="font-size:.78rem;color:#aaa;margin-top:4px;">For your profile picture only</p>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label for="location" class="nd-form-label">Location</label>
                <input type="text" id="location" name="location" class="nd-form-input" placeholder="Enter your city / location (optional)">
            </div>
            <div style="margin-bottom:20px;">
                <label for="text" class="nd-form-label">Your Review <span style="color:#E84855;">*</span></label>
                <textarea id="text" name="text" required rows="4" class="nd-form-textarea nd-form-input" placeholder="Share your experience with us…"></textarea>
            </div>
            <div id="formMessage" class="hidden" style="padding:12px 16px;border-radius:10px;font-size:.875rem;margin-bottom:16px;"></div>
            <button type="submit" id="submitBtn" class="nd-btn nd-btn-orange" style="width:100%;justify-content:center;">
                <span id="submitBtnText">Submit Review</span>
                <span id="submitBtnLoading" class="hidden">Submitting…</span>
            </button>
        </form>
    </div>
</div>

<?php if (empty($testimonials)): ?>
    <p style="text-align:center;color:#999;">No reviews yet. Check back soon.</p>
<?php else: ?>

<!-- Unified reviews grid — all screen sizes -->
<div class="nd-reviews-grid" data-aos="fade-up">
    <?php foreach ($displayedReviews as $index => $testimonial):
        $photoPath = !empty($testimonial['photo_path']) ? $testimonial['photo_path'] : null;
        $hasPhoto  = $photoPath && file_exists(__DIR__ . '/' . $photoPath);
        $color     = $avatarColors[$index % count($avatarColors)];
        $initials  = strtoupper(substr($testimonial['author'], 0, 1));
    ?>
        <div class="nd-review-card nd-review-clickable"
             data-full="<?php echo htmlspecialchars($testimonial['text']); ?>"
             data-author="<?php echo htmlspecialchars($testimonial['author']); ?>"
             data-location="<?php echo htmlspecialchars($testimonial['location'] ?? ''); ?>"
             data-color="<?php echo $color; ?>"
             data-photo="<?php echo $hasPhoto ? htmlspecialchars($photoPath) : ''; ?>"
             data-initials="<?php echo $initials; ?>"
             onclick="ndOpenReview(this)">
            <div class="nd-review-author">
                <div class="nd-review-avatar">
                    <?php if ($hasPhoto): ?>
                        <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="<?php echo htmlspecialchars($testimonial['author']); ?>">
                    <?php else: ?>
                        <div style="background:<?php echo $color; ?>;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;color:#fff;">
                            <?php echo $initials; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="nd-review-name"><?php echo htmlspecialchars($testimonial['author']); ?></div>
                    <?php if (!empty($testimonial['location'])): ?>
                        <div class="nd-review-loc"><?php echo htmlspecialchars($testimonial['location']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="nd-review-text nd-review-text-clamp">"<?php echo htmlspecialchars($testimonial['text']); ?>"</div>
            <span class="nd-read-more-link">Read more →</span>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($hasMoreReviews): ?>
<div style="text-align:center;margin-top:32px;" data-aos="fade-up" data-aos-delay="300">
    <a href="<?php echo htmlspecialchars(BASE_URL . 'reviews.php'); ?>" class="nd-btn nd-btn-orange">
        View All Reviews
        <span style="background:rgba(255,255,255,.25);padding:3px 10px;border-radius:50px;font-size:.78rem;">+<?php echo $remainingCount; ?></span>
    </a>
</div>
<?php endif; ?>

<!-- Review full-text popup -->
<div id="ndReviewPopup" class="nd-review-popup" onclick="if(event.target===this)ndCloseReview()">
    <div class="nd-review-popup-inner">
        <button class="nd-review-popup-close" onclick="ndCloseReview()" aria-label="Close">&times;</button>
        <div id="ndReviewPopupContent"></div>
    </div>
</div>

<?php endif; ?>
<?php
$section_content = ob_get_clean();
include __DIR__ . '/includes/landing/section.php';
?>


<!-- =====================================================================
     CONTACT  →  Three contact info cards
     ===================================================================== -->
<?php
$section_title    = 'Contact Us';
$section_bg_color = 'bg-white';
$section_id       = 'contact';
ob_start();
?>
<p style="text-align:center;color:#777;margin-bottom:44px;font-size:1rem;max-width:520px;margin-left:auto;margin-right:auto;" data-aos="fade-up">
    Get in touch with us to schedule a consultation or learn more about our services.
</p>

<div class="nd-contact-grid nd-contact-home-hide">
    <!-- Address -->
    <div class="nd-contact-card" data-aos="fade-up" data-aos-delay="100">
        <div class="nd-contact-icon-wrap">
            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="nd-contact-title">Address</div>
        <div class="nd-contact-info">Dehrakhas, Patel Nagar,<br>Dehradun, Uttarakhand</div>
    </div>

    <!-- Phone -->
    <div class="nd-contact-card" data-aos="fade-up" data-aos-delay="200">
        <div class="nd-contact-icon-wrap">
            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
        <div class="nd-contact-title">Phone</div>
        <div class="nd-contact-info">
            <a href="tel:9634579408">+91 9634579408</a>
        </div>
    </div>

    <!-- Email -->
    <div class="nd-contact-card" data-aos="fade-up" data-aos-delay="300">
        <div class="nd-contact-icon-wrap">
            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="nd-contact-title">Email</div>
        <div class="nd-contact-info">
            <a href="mailto:info@neoranewbi.in">info@neoranewbi.in</a>
        </div>
    </div>
</div>
<?php
$section_content = ob_get_clean();
include __DIR__ . '/includes/landing/section.php';
?>


<?php include __DIR__ . '/includes/landing/footer.php'; ?>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialise AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 1000, easing: 'ease-in-out', once: true, offset: 80 });
    }

    // Collapsible review form toggle
    const reviewFormToggle  = document.getElementById('reviewFormToggle');
    const reviewFormContent = document.getElementById('reviewFormContent');

    if (reviewFormToggle && reviewFormContent) {
        reviewFormToggle.addEventListener('click', function () {
            const isHidden = reviewFormContent.classList.contains('hidden');
            if (isHidden) {
                reviewFormContent.classList.remove('hidden');
                reviewFormContent.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                reviewFormContent.classList.add('hidden');
            }
        });
    }

    // Photo preview
    const photoInput  = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const previewImg  = document.getElementById('previewImg');

    if (photoInput) {
        photoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                photoPreview.classList.add('hidden');
            }
        });
    }

    // Review form submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn        = document.getElementById('submitBtn');
            const submitBtnText    = document.getElementById('submitBtnText');
            const submitBtnLoading = document.getElementById('submitBtnLoading');
            const formMessage      = document.getElementById('formMessage');

            submitBtn.disabled = true;
            submitBtnText.classList.add('hidden');
            submitBtnLoading.classList.remove('hidden');
            formMessage.classList.add('hidden');

            const formData = new FormData(reviewForm);

            try {
                const response = await fetch('<?php echo BASE_URL; ?>api/submit_review.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    formMessage.textContent = result.message || 'Thank you for your review! It has been submitted successfully.';
                    formMessage.style.cssText = 'display:block;background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;';
                    formMessage.classList.remove('hidden');
                    reviewForm.reset();
                    if (photoPreview) photoPreview.classList.add('hidden');
                    setTimeout(() => { window.location.reload(); }, 2000);
                } else {
                    formMessage.textContent = result.message || 'Failed to submit review. Please try again.';
                    formMessage.style.cssText = 'display:block;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;';
                    formMessage.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtnText.classList.remove('hidden');
                    submitBtnLoading.classList.add('hidden');
                }
            } catch (error) {
                formMessage.textContent = 'An error occurred. Please try again later.';
                formMessage.style.cssText = 'display:block;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;';
                formMessage.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtnText.classList.remove('hidden');
                submitBtnLoading.classList.add('hidden');
            }
        });
    }
}); // end DOMContentLoaded

/* ── Mobile booking form ────────────────────────────────── */
window.ndMobileBooking = function () {
    var name    = document.getElementById('mbName').value.trim();
    var phone   = document.getElementById('mbPhone').value.trim();
    var service = document.getElementById('mbService').value;
    var msgEl   = document.getElementById('mbMsg');
    var btn     = document.getElementById('mbSubmitBtn');
    var btnText = document.getElementById('mbSubmitBtnText');

    function showErr(txt) {
        msgEl.innerHTML = txt;
        msgEl.style.cssText = 'display:block;padding:10px 14px;border-radius:10px;font-size:.8rem;margin-top:4px;background:#f8d7da;color:#58151c;border:1px solid #f1aeb5;';
    }
    if (!name || !phone) { showErr('Please enter your name and phone number.'); return; }

    msgEl.style.display = 'none';
    btn.disabled = true;
    btnText.innerHTML = 'Submitting…';

    var fd = new FormData();
    fd.append('name',    name);
    fd.append('phone',   phone);
    fd.append('service', service || '');

    fetch('<?php echo BASE_URL; ?>api/book_session.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res){
            if (res.success) {
                document.getElementById('mbName').value    = '';
                document.getElementById('mbPhone').value   = '';
                document.getElementById('mbService').value = '';
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
            btnText.innerHTML = 'Request Appointment <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>';
        });
};

/* ── Review popup ───────────────────────────────────────── */
window.ndOpenReview = function (card) {
    var full     = card.dataset.full     || '';
    var author   = card.dataset.author   || '';
    var location = card.dataset.location || '';
    var color    = card.dataset.color    || '#B45309';
    var photo    = card.dataset.photo    || '';
    var initials = card.dataset.initials || '?';

    var avatarHtml = photo
        ? '<img src="' + photo + '" alt="' + author + '" style="width:100%;height:100%;object-fit:cover;">'
        : '<div style="background:' + color + ';width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.4rem;color:#fff;">' + initials + '</div>';

    var locHtml = location
        ? '<div style="font-size:.8rem;color:#aaa;margin-top:3px;">' + location + '</div>'
        : '';

    document.getElementById('ndReviewPopupContent').innerHTML =
        '<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">' +
            '<div class="nd-popup-avatar">' + avatarHtml + '</div>' +
            '<div><div style="font-family:var(--heading-font);font-size:1.25rem;color:var(--black-color);">' + author + '</div>' + locHtml + '</div>' +
        '</div>' +
        '<p style="font-style:italic;color:#555;font-size:.95rem;line-height:1.8;margin:0;">&ldquo;' + full + '&rdquo;</p>';

    document.getElementById('ndReviewPopup').classList.add('open');
    document.body.style.overflow = 'hidden';
};

window.ndCloseReview = function () {
    document.getElementById('ndReviewPopup').classList.remove('open');
    document.body.style.overflow = '';
};
</script>
</body>
</html>
