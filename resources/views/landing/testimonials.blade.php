@php
$pageTitle = 'Testimonials';
$pageDesc  = 'Read what families say about NEORA: real reviews from real patients who have experienced the NEORA difference.';
@endphp
@include('landing.partials.page_head')

@include('landing.partials.navbar')

<!-- ── Page Hero ─────────────────────────────────────── -->
<div class="nd-page-hero" data-aos="fade-in">
    <div class="nd-page-hero-bg" style="background-image:url('{{ landing_asset('landing/images/494bd119-204d-4c5f-9b19-29fb0ae98677.JPG') }}');background-position:center 25%;"></div>
    <div class="nd-page-hero-overlay"></div>
    <div class="nd-page-hero-content">
        <div class="container-fluid px-4 px-lg-5">
            <div class="nd-breadcrumb">
                <a href="{{ $baseUrl }}">Home</a>
                <span>›</span>
                <span style="color:rgba(255,255,255,.75);">Testimonials</span>
            </div>
            <p class="nd-page-hero-eyebrow">Family Stories</p>
            <h1 class="nd-page-hero-title">What Families <span>Say</span></h1>
            <p class="nd-page-hero-sub">Honest reviews from patients and families who have experienced the NEORA difference.</p>
        </div>
    </div>
</div>


<!-- ── Reviews Section ───────────────────────────────── -->
<?php
$testimonials = [];
try {
    $stmt = $pdo->query("SELECT id, `text`, author, `location`, photo_path FROM reviews ORDER BY display_order ASC, id ASC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // keep empty
}

$totalReviews = count($testimonials);
$avatarColors = ['#B45309','#0D9488','#475569','#B91C1C','#4338CA','#059669'];
?>

<section class="nd-section bg-gray-50" id="testimonials">
    <div class="nd-section-inner">

        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:48px;" data-aos="fade-up">
            <span class="nd-section-label">Reviews from Families</span>
            <h2 class="nd-section-title">Real <span class="nd-accent">Stories</span>, Real Results</h2>
            <?php if ($totalReviews > 0): ?>
            <p class="nd-section-text" style="margin-left:auto;margin-right:auto;max-width:480px;">
                <?php echo $totalReviews; ?> families have shared their journey with NEORA.
            </p>
            <?php endif; ?>
        </div>

        <?php if (empty($testimonials)): ?>
            <p style="text-align:center;color:#999;padding:40px 0;">No reviews yet. Check back soon.</p>
        <?php else: ?>
            <div class="nd-reviews-grid" data-aos="fade-up">
                <?php foreach ($testimonials as $index => $testimonial):
                    $photoPath = !empty($testimonial['photo_path']) ? $testimonial['photo_path'] : null;
                ?>
                    <div class="nd-review-card" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 80; ?>">
                        <div class="nd-review-author">
                            <div class="nd-review-avatar">
                                <?php if ($photoPath && file_exists(public_path($photoPath))): ?>
                                    <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="<?php echo htmlspecialchars($testimonial['author']); ?>">
                                <?php else: ?>
                                    <div style="background:<?php echo $avatarColors[$index % count($avatarColors)]; ?>;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;color:#fff;">
                                        <?php echo strtoupper(substr($testimonial['author'], 0, 1)); ?>
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
                        <div class="nd-review-text">"<?php echo htmlspecialchars($testimonial['text']); ?>"</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ── Write a Review ─────────────────────────── -->
        <div style="max-width:680px;margin:60px auto 0;" data-aos="fade-up" data-aos-delay="200">
            <div style="text-align:center;margin-bottom:20px;">
                <button type="button" id="reviewFormToggle" class="nd-write-review-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Write a Review
                </button>
            </div>

            <div id="reviewFormContent" class="nd-review-form-wrap hidden">
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
                                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/jpg,image/png"
                                           class="nd-form-input" style="padding:10px 12px;cursor:pointer;">
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
        </div>

    </div>
</section>


@include('landing.partials.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });

    // Review form toggle
    var toggle  = document.getElementById('reviewFormToggle');
    var content = document.getElementById('reviewFormContent');
    if (toggle && content) {
        toggle.addEventListener('click', function () {
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                content.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                content.classList.add('hidden');
            }
        });
    }

    // Photo preview
    var photoInput  = document.getElementById('photo');
    var photoPreview = document.getElementById('photoPreview');
    var previewImg  = document.getElementById('previewImg');
    if (photoInput) {
        photoInput.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
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

    // Review submission
    var reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var submitBtn        = document.getElementById('submitBtn');
            var submitBtnText    = document.getElementById('submitBtnText');
            var submitBtnLoading = document.getElementById('submitBtnLoading');
            var formMessage      = document.getElementById('formMessage');

            submitBtn.disabled = true;
            submitBtnText.classList.add('hidden');
            submitBtnLoading.classList.remove('hidden');
            formMessage.classList.add('hidden');

            try {
                var response = await fetch('{{ route('api.submit-review') }}', {
                    method: 'POST', body: new FormData(reviewForm)
                });
                var result = await response.json();

                if (result.success) {
                    formMessage.textContent = result.message || 'Thank you for your review!';
                    formMessage.style.cssText = 'display:block;background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;';
                    formMessage.classList.remove('hidden');
                    reviewForm.reset();
                    photoPreview.classList.add('hidden');
                    setTimeout(function () { window.location.reload(); }, 2000);
                } else {
                    formMessage.textContent = result.message || 'Failed to submit. Please try again.';
                    formMessage.style.cssText = 'display:block;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;';
                    formMessage.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtnText.classList.remove('hidden');
                    submitBtnLoading.classList.add('hidden');
                }
            } catch (err) {
                formMessage.textContent = 'An error occurred. Please try again later.';
                formMessage.style.cssText = 'display:block;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;';
                formMessage.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtnText.classList.remove('hidden');
                submitBtnLoading.classList.add('hidden');
            }
        });
    }
});
</script>
</body>
</html>
