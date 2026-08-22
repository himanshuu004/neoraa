<?php
// Footer Component, Mellow-inspired dark design
$footerBase = $baseUrl;
?>

<section id="footer-section" data-aos="fade-up">
    <div class="footer-inner">
        <div class="footer-grid">

            <!-- Column 1: Brand + Social -->
            <div>
                <a href="{{ $baseUrl }}">
                    <img
                        src="{{ landing_asset('landing/logo/logo.webp') }}"
                        alt="NEORA Logo"
                        class="footer-brand-logo"
                        style="filter:brightness(0) invert(1);"
                    />
                </a>
                <p class="footer-brand-desc">
                    Leading speech therapy and audiology clinic dedicated to helping
                    individuals of all ages improve their communication and hearing abilities.
                    Personalised, evidence-based care for every stage of life.
                </p>
                <div class="footer-social">
                    <a href="https://www.instagram.com/neora_newbies/" target="_blank" rel="noopener noreferrer"
                       class="footer-social-link" aria-label="Instagram">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M128 80a48 48 0 1 0 48 48a48.05 48.05 0 0 0-48-48Zm0 80a32 32 0 1 1 32-32a32 32 0 0 1-32 32Zm48-136H80a56.06 56.06 0 0 0-56 56v96a56.06 56.06 0 0 0 56 56h96a56.06 56.06 0 0 0 56-56V80a56.06 56.06 0 0 0-56-56Zm40 152a40 40 0 0 1-40 40H80a40 40 0 0 1-40-40V80a40 40 0 0 1 40-40h96a40 40 0 0 1 40 40ZM192 76a12 12 0 1 1-12-12a12 12 0 0 1 12 12Z"/>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/company/neoranewbies/" target="_blank" rel="noopener noreferrer"
                       class="footer-social-link" aria-label="LinkedIn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M444.17 32H70.28C49.85 32 32 46.7 32 66.89v374.72C32 461.91 49.85 480 70.28 480h373.78c20.54 0 35.94-18.21 35.94-38.39V66.89C480.12 46.7 464.6 32 444.17 32Zm-273.3 373.43h-64.18V205.88h64.18ZM141 175.54h-.46c-20.54 0-33.84-15.29-33.84-34.43c0-19.49 13.65-34.42 34.65-34.42s33.85 14.82 34.31 34.42c-.01 19.14-13.31 34.43-34.66 34.43Zm264.43 229.89h-64.18V296.32c0-26.14-9.34-44-32.56-44c-17.74 0-28.24 12-32.91 23.69c-1.75 4.2-2.22 9.92-2.22 15.76v113.66h-64.18V205.88h64.18v27.77c9.34-13.3 23.93-32.44 57.88-32.44c42.13 0 74 27.77 74 87.64Z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 class="footer-col-title">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('services') }}">Our Services</a></li>
                    <li><a href="{{ route('resources.index') }}">Resources</a></li>
                    <li><a href="{{ route('gallery') }}">Gallery</a></li>
                    <li><a href="{{ route('testimonials') }}">Testimonials</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('login') }}">Sign In</a></li>
                </ul>
            </div>

            <!-- Column 3: Contact Info -->
            <div>
                <h4 class="footer-col-title">Contact Us</h4>
                <div class="footer-info-item">
                    <svg width="16" height="16" fill="none" stroke="rgba(255,255,255,.6)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Dehrakhas, Patel Nagar,<br>Dehradun, Uttarakhand</span>
                </div>
                <div class="footer-info-item">
                    <svg width="16" height="16" fill="none" stroke="rgba(255,255,255,.6)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <a href="tel:9634579408">9634579408</a>
                </div>
                <div class="footer-info-item">
                    <svg width="16" height="16" fill="none" stroke="rgba(255,255,255,.6)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <a href="mailto:info@neoranewbi.in">info@neoranewbi.in</a>
                </div>
            </div>

            <!-- Column 4: Opening Hours + CTA -->
            <div>
                <h4 class="footer-col-title">Opening Hours</h4>
                <ul class="footer-hours">
                    <li><span>Monday – Friday</span><span>9:00 AM – 6:00 PM</span></li>
                    <li><span>Saturday</span><span>9:00 AM – 2:00 PM</span></li>
                    <li><span>Sunday</span><span>Closed</span></li>
                </ul>
                <a href="{{ route('contact') }}#book" class="btn btn-arrow btn-primary">
                    <span>Book Appointment
                        <svg width="18" height="18"><use xlink:href="#arrow-right"></use></svg>
                    </span>
                </a>
            </div>

        </div><!-- /.footer-grid -->

        <!-- Bottom bar -->
        <div class="footer-bottom">
            <p class="footer-copy m-0">
                &copy; <?php echo date('Y'); ?> NEORA Therapy &amp; Audiology Clinic. All rights reserved.
            </p>
            <div class="footer-legal">
                <a href="#about">Privacy Policy</a>
                <a href="#about">Terms of Service</a>
            </div>
        </div>

    </div>
</section>

<!-- ── Booking Confirmation Popup ─────────────────────────────────── -->
<div id="ndBookingPopup" aria-modal="true" role="dialog" aria-labelledby="ndPopupTitle"
     style="display:none;position:fixed;inset:0;z-index:99999;align-items:center;justify-content:center;padding:20px;">

    <!-- Backdrop -->
    <div id="ndPopupBackdrop"
         style="position:absolute;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);"
         onclick="ndCloseBookingPopup()"></div>

    <!-- Card -->
    <div id="ndPopupCard"
         style="position:relative;z-index:1;background:#fff;border-radius:24px;padding:44px 36px 36px;max-width:420px;width:100%;
                box-shadow:0 24px 80px rgba(0,0,0,.22);text-align:center;
                transform:scale(.88) translateY(20px);opacity:0;
                transition:transform .35s cubic-bezier(.34,1.56,.64,1), opacity .3s ease;">

        <!-- Close button -->
        <button onclick="ndCloseBookingPopup()" aria-label="Close"
                style="position:absolute;top:16px;right:16px;background:none;border:none;cursor:pointer;
                       width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                       color:#aaa;font-size:1.2rem;transition:background .2s,color .2s;"
                onmouseover="this.style.background='#f5f5f5';this.style.color='#333';"
                onmouseout="this.style.background='none';this.style.color='#aaa';">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <!-- Icon circle -->
        <div id="ndPopupIconWrap"
             style="width:80px;height:80px;border-radius:50%;margin:0 auto 22px;display:flex;align-items:center;justify-content:center;">
        </div>

        <!-- Content -->
        <h2 id="ndPopupTitle" style="font-size:1.35rem;font-weight:700;color:#1a1a2e;margin:0 0 10px;font-family:var(--heading-font,inherit);"></h2>
        <p  id="ndPopupBody"  style="font-size:.9rem;color:#666;line-height:1.65;margin:0 0 28px;"></p>

        <!-- Got it button -->
        <button onclick="ndCloseBookingPopup()"
                id="ndPopupBtn"
                style="display:inline-flex;align-items:center;justify-content:center;gap:8px;
                       padding:13px 36px;border-radius:100px;border:none;cursor:pointer;
                       font-size:.9rem;font-weight:700;font-family:var(--body-font,inherit);
                       transition:opacity .2s,transform .15s;"
                onmouseover="this.style.opacity='.88';this.style.transform='scale(1.03)';"
                onmouseout="this.style.opacity='1';this.style.transform='scale(1)';">
            Got it &nbsp;✓
        </button>
    </div>
</div>

<style>
#ndBookingPopup.nd-popup-open { display:flex !important; }

@media (max-width: 640px) {
    #ndPopupCard {
        padding: 32px 20px 24px !important;
        border-radius: 18px !important;
        max-width: 92vw !important;
    }
    #ndPopupIconWrap {
        width: 56px !important;
        height: 56px !important;
        margin-bottom: 14px !important;
    }
    #ndPopupIconWrap svg { width: 26px !important; height: 26px !important; }
    #ndPopupTitle { font-size: 1.1rem !important; margin-bottom: 8px !important; }
    #ndPopupBody  { font-size: .82rem !important; margin-bottom: 20px !important; }
    #ndPopupBtn   { padding: 11px 28px !important; font-size: .84rem !important; }
}
</style>

<script>
(function(){
    /* ndShowBookingPopup(type, name)
       type: 'success' | 'duplicate' | 'error'
       name: optional first name to personalise the message
    */
    window.ndShowBookingPopup = function(type, name) {
        var popup    = document.getElementById('ndBookingPopup');
        var card     = document.getElementById('ndPopupCard');
        var iconWrap = document.getElementById('ndPopupIconWrap');
        var title    = document.getElementById('ndPopupTitle');
        var body     = document.getElementById('ndPopupBody');
        var btn      = document.getElementById('ndPopupBtn');

        var hi = name ? 'Hi ' + name.split(' ')[0] + '! ' : '';

        if (type === 'success') {
            iconWrap.style.background = '#e8f5e9';
            iconWrap.innerHTML = '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>';
            title.textContent  = 'Request Submitted!';
            body.innerHTML     = hi + 'Your booking request has been received.<br>The <strong>NEORA team</strong> will reach out to you shortly, usually within 24 hours.';
            btn.style.background   = 'var(--primary-color, #c2527a)';
            btn.style.color        = '#fff';
        } else if (type === 'duplicate') {
            iconWrap.style.background = '#fff8e1';
            iconWrap.innerHTML = '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="#f59e0b"/></svg>';
            title.textContent  = 'Already Submitted';
            body.innerHTML     = hi + 'We already have a booking request with this contact number.<br>Our team will reach out to you, <strong>no need to fill again!</strong>';
            btn.style.background   = '#f59e0b';
            btn.style.color        = '#fff';
        } else {
            iconWrap.style.background = '#fce8ec';
            iconWrap.innerHTML = '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="#c0392b"/></svg>';
            title.textContent  = 'Something went wrong';
            body.textContent   = 'We could not submit your request right now. Please try again or call us directly.';
            btn.style.background   = '#c0392b';
            btn.style.color        = '#fff';
        }

        // Show
        popup.classList.add('nd-popup-open');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(function(){
            requestAnimationFrame(function(){
                card.style.transform = 'scale(1) translateY(0)';
                card.style.opacity   = '1';
            });
        });
    };

    window.ndCloseBookingPopup = function() {
        var popup = document.getElementById('ndBookingPopup');
        var card  = document.getElementById('ndPopupCard');
        card.style.transform = 'scale(.88) translateY(20px)';
        card.style.opacity   = '0';
        setTimeout(function(){
            popup.classList.remove('nd-popup-open');
            document.body.style.overflow = '';
        }, 300);
    };

    // ESC key to close
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') window.ndCloseBookingPopup();
    });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS === 'undefined' || window.__ndAosInit) return;
    window.__ndAosInit = true;
    AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });
});
</script>
