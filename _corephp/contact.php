<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Contact Us';
$pageDesc  = 'Get in touch with NEORA Therapy & Audiology Clinic — book a session, ask a question, or find us in Dehradun.';
include __DIR__ . '/includes/landing/page_head.php';
?>

<?php include __DIR__ . '/includes/landing/navbar.php'; ?>

<!-- ── Page Hero ─────────────────────────────────────── -->
<div class="nd-page-hero" data-aos="fade-in">
    <div class="nd-page-hero-bg" style="background-image:url('public/landing/images/e3adc225-888a-426e-9deb-70bc753adbc8.JPG');background-position:center 30%;"></div>
    <div class="nd-page-hero-overlay"></div>
    <div class="nd-page-hero-content">
        <div class="container-fluid px-4 px-lg-5">
            <div class="nd-breadcrumb">
                <a href="<?php echo BASE_URL; ?>">Home</a>
                <span>›</span>
                <span style="color:rgba(255,255,255,.75);">Contact</span>
            </div>
            <p class="nd-page-hero-eyebrow">Get in Touch</p>
            <h1 class="nd-page-hero-title">Contact <span>NEORA</span></h1>
            <p class="nd-page-hero-sub">We'd love to hear from you — book a session, ask a question, or find your way to our clinic.</p>
        </div>
    </div>
</div>


<!-- ── Contact Cards ─────────────────────────────────── -->
<section class="nd-section bg-white" id="contact">
    <div class="nd-section-inner">

        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:48px;" data-aos="fade-up">
            <span class="nd-section-label">Reach Us</span>
            <h2 class="nd-section-title">How to <span class="nd-accent">Connect</span></h2>
            <p class="nd-section-text" style="margin-left:auto;margin-right:auto;max-width:480px;">
                Get in touch to schedule a consultation or learn more about our services.
            </p>
        </div>

        <div class="nd-contact-grid" data-aos="fade-up" data-aos-delay="100">
            <!-- Address -->
            <div class="nd-contact-card">
                <div class="nd-contact-icon-wrap">
                    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="nd-contact-title">Address</div>
                <div class="nd-contact-info">Dehrakhas, Patel Nagar,<br>Dehradun, Uttarakhand</div>
                <a href="https://maps.google.com/?q=Patel+Nagar+Dehradun" target="_blank" rel="noopener"
                   style="display:inline-block;margin-top:14px;font-size:.8rem;font-weight:600;color:var(--primary-color);text-decoration:none;">
                    Get Directions →
                </a>
            </div>

            <!-- Phone -->
            <div class="nd-contact-card">
                <div class="nd-contact-icon-wrap">
                    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div class="nd-contact-title">Phone</div>
                <div class="nd-contact-info"><a href="tel:9634579408">9634579408</a></div>
                <a href="tel:9634579408"
                   style="display:inline-block;margin-top:14px;font-size:.8rem;font-weight:600;color:var(--primary-color);text-decoration:none;">
                    Call Now →
                </a>
            </div>

            <!-- Email -->
            <div class="nd-contact-card">
                <div class="nd-contact-icon-wrap">
                    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="nd-contact-title">Email</div>
                <div class="nd-contact-info"><a href="mailto:info@neoranewbi.in">info@neoranewbi.in</a></div>
                <a href="mailto:info@neoranewbi.in"
                   style="display:inline-block;margin-top:14px;font-size:.8rem;font-weight:600;color:var(--primary-color);text-decoration:none;">
                    Send Email →
                </a>
            </div>
        </div>
    </div>
</section>


<!-- ── Book a Session Form + Hours ───────────────────── -->
<section class="nd-section bg-gray-50" id="book">
    <div class="nd-section-inner">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:start;padding:0 1rem;" class="nd-contact-form-grid">

            <!-- Left: Book a Session form -->
            <div data-aos="fade-right">
                <span class="nd-section-label">Quick Booking</span>
                <h2 class="nd-section-title" style="margin-bottom:8px;">Book a <span class="nd-accent">Session</span></h2>
                <p class="nd-section-text" style="margin-bottom:28px;">
                    Fill in your details and we'll get back to you within 24 hours to confirm your appointment.
                </p>

                <form id="ndContactForm" style="display:flex;flex-direction:column;gap:16px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label class="nd-form-label">Full Name <span style="color:#E84855;">*</span></label>
                            <input type="text" name="name" required placeholder="Your name" class="nd-form-input">
                        </div>
                        <div>
                            <label class="nd-form-label">Phone Number <span style="color:#E84855;">*</span></label>
                            <input type="tel" name="phone" required placeholder="10-digit number" class="nd-form-input">
                        </div>
                    </div>

                    <div>
                        <label class="nd-form-label">Service Required</label>
                        <select name="service" class="nd-form-input" style="appearance:none;-webkit-appearance:none;cursor:pointer;">
                            <option value="">— Select a service —</option>
                            <option>Speech Therapy</option>
                            <option>Audiology Services</option>
                            <option>Language Therapy</option>
                            <option>Occupational Therapy</option>
                            <option>Special Education</option>
                            <option>Early Intervention</option>
                            <option>Hearing Aid Fitting</option>
                            <option>Consult</option>
                            <option>Other / Not Sure</option>
                        </select>
                    </div>

                    <div>
                        <label class="nd-form-label">Message <span style="font-weight:400;color:#aaa;">(optional)</span></label>
                        <textarea name="message" rows="3" placeholder="Any additional information…" class="nd-form-textarea nd-form-input"></textarea>
                    </div>

                    <div id="contactFormMsg" style="display:none;padding:12px 16px;border-radius:10px;font-size:.875rem;"></div>

                    <button type="submit" class="nd-btn nd-btn-orange" style="justify-content:center;" id="contactSubmitBtn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:4px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span id="contactBtnText">Submit Booking Request</span>
                    </button>
                    <p style="font-size:.75rem;color:#aaa;text-align:center;margin-top:-8px;">
                        We'll get back to you within 24 hours to confirm your appointment.
                    </p>
                </form>
            </div>

            <!-- Right: Clinic hours + quick links -->
            <div data-aos="fade-left" data-aos-delay="150">
                <span class="nd-section-label">Clinic Hours</span>
                <h2 class="nd-section-title" style="margin-bottom:24px;">Opening <span class="nd-accent">Hours</span></h2>

                <div style="background:#fff;border-radius:20px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.07);margin-bottom:28px;">
                    <?php
                    $hours = [
                        ['day'=>'Monday – Friday',  'time'=>'9:00 AM – 6:00 PM', 'open'=>true],
                        ['day'=>'Saturday',          'time'=>'9:00 AM – 4:00 PM', 'open'=>true],
                        ['day'=>'Sunday',            'time'=>'Closed',             'open'=>false],
                    ];
                    ?>
                    <?php foreach ($hours as $i => $h): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;<?php echo $i > 0 ? 'border-top:1px solid #f0e8f2;' : ''; ?>">
                        <span style="font-size:.88rem;font-weight:600;color:var(--black-color);"><?php echo $h['day']; ?></span>
                        <span style="font-size:.85rem;font-weight:600;color:<?php echo $h['open'] ? 'var(--green-color)' : '#e05577'; ?>;">
                            <?php echo $h['time']; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Social links -->
                <div>
                    <p style="font-size:.78rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#aaa;margin-bottom:14px;">Follow Us</p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="https://www.instagram.com/neora_newbies/" target="_blank" rel="noopener"
                           style="display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:100px;background:#fdf4f8;border:1.5px solid rgba(223,85,137,.20);font-size:.82rem;font-weight:600;color:var(--primary-color);text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.background='var(--primary-color)';this.style.color='#fff';"
                           onmouseout="this.style.background='#fdf4f8';this.style.color='var(--primary-color)';">
                            <svg width="16" height="16" viewBox="0 0 256 256" fill="currentColor"><path d="M128 80a48 48 0 1 0 48 48a48.05 48.05 0 0 0-48-48Zm0 80a32 32 0 1 1 32-32a32 32 0 0 1-32 32Zm48-136H80a56.06 56.06 0 0 0-56 56v96a56.06 56.06 0 0 0 56 56h96a56.06 56.06 0 0 0 56-56V80a56.06 56.06 0 0 0-56-56Zm40 152a40 40 0 0 1-40 40H80a40 40 0 0 1-40-40V80a40 40 0 0 1 40-40h96a40 40 0 0 1 40 40ZM192 76a12 12 0 1 1-12-12a12 12 0 0 1 12 12Z"/></svg>
                            Instagram
                        </a>
                        <a href="https://www.linkedin.com/company/neoranewbies/" target="_blank" rel="noopener"
                           style="display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:100px;background:#f0faf8;border:1.5px solid rgba(161,196,74,.25);font-size:.82rem;font-weight:600;color:var(--green-color);text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.background='var(--green-color)';this.style.color='#fff';"
                           onmouseout="this.style.background='#f0faf8';this.style.color='var(--green-color)';">
                            <svg width="16" height="16" viewBox="0 0 512 512" fill="currentColor"><path d="M444.17 32H70.28C49.85 32 32 46.7 32 66.89v374.72C32 461.91 49.85 480 70.28 480h373.78c20.54 0 35.94-18.21 35.94-38.39V66.89C480.12 46.7 464.6 32 444.17 32Zm-273.3 373.43h-64.18V205.88h64.18ZM141 175.54h-.46c-20.54 0-33.84-15.29-33.84-34.43c0-19.49 13.65-34.42 34.65-34.42s33.85 14.82 34.31 34.42c-.01 19.14-13.31 34.43-34.66 34.43Zm264.43 229.89h-64.18V296.32c0-26.14-9.34-44-32.56-44c-17.74 0-28.24 12-32.91 23.69c-1.75 4.2-2.22 9.92-2.22 15.76v113.66h-64.18V205.88h64.18v27.77c9.34-13.3 23.93-32.44 57.88-32.44c42.13 0 74 27.77 74 87.64Z"/></svg>
                            LinkedIn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ── Google Map ─────────────────────────────────────── -->
<section style="height:400px;position:relative;" data-aos="fade-up">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3444.5!2d77.9952!3d30.3165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390929c356c1fa4d%3A0x8bede74a57de0a0e!2sPatel%20Nagar%2C%20Dehradun%2C%20Uttarakhand!5e0!3m2!1sen!2sin!4v1700000000000"
        width="100%" height="400" style="border:0;display:block;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade" title="NEORA Clinic Location">
    </iframe>
    <!-- Overlay card on map -->
    <div style="position:absolute;top:24px;left:32px;background:#fff;border-radius:16px;padding:20px 24px;box-shadow:0 8px 32px rgba(0,0,0,.15);max-width:260px;z-index:2;">
        <div style="font-family:var(--heading-font);font-size:1.2rem;font-weight:400;color:var(--black-color);margin-bottom:6px;">NEORA Clinic</div>
        <div style="font-size:.82rem;color:var(--gray-color);line-height:1.6;">
            Dehrakhas, Patel Nagar,<br>Dehradun, Uttarakhand
        </div>
        <a href="https://maps.google.com/?q=Patel+Nagar+Dehradun" target="_blank" rel="noopener"
           style="display:inline-block;margin-top:10px;font-size:.78rem;font-weight:700;color:var(--primary-color);text-decoration:none;">
            Open in Google Maps →
        </a>
    </div>
</section>


<?php include __DIR__ . '/includes/landing/footer.php'; ?>

<style>
    @media (max-width: 767px) {
        .nd-contact-form-grid {
            grid-template-columns: 1fr !important;
            gap: 40px !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });

    var form    = document.getElementById('ndContactForm');
    var msgEl   = document.getElementById('contactFormMsg');
    var btn     = document.getElementById('contactSubmitBtn');
    var btnText = document.getElementById('contactBtnText');

    function showInlineErr(text) {
        msgEl.innerHTML  = text;
        msgEl.style.cssText = 'display:block;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:.875rem;';
        msgEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var name    = form.querySelector('[name="name"]').value.trim();
            var phone   = form.querySelector('[name="phone"]').value.trim();
            var service = form.querySelector('[name="service"]').value;
            var message = form.querySelector('[name="message"]').value.trim();

            if (!name || !phone) {
                showInlineErr('&#10007; Please fill in your name and phone number.');
                return;
            }

            msgEl.style.display = 'none';
            btn.disabled = true;
            btnText.textContent = 'Submitting…';

            var fd = new FormData();
            fd.append('name',    name);
            fd.append('phone',   phone);
            fd.append('service', service);
            fd.append('message', message);

            fetch('<?php echo BASE_URL; ?>api/book_session.php', { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(res){
                    if (res.success) {
                        form.reset();
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
                    btnText.textContent = 'Submit Booking Request';
                });
        });
    }
});
</script>
</body>
</html>
