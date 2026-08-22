@php
$pageTitle = 'Our Services';
$pageDesc  = 'Discover NEORA\'s full range of services: Speech Therapy, Audiology, Language Therapy, Occupational Therapy, Special Education, and Early Intervention.';
@endphp
@include('landing.partials.page_head')

@include('landing.partials.navbar')

<!-- ── Page Hero ─────────────────────────────────────── -->
<div class="nd-page-hero" data-aos="fade-in">
    <div class="nd-page-hero-bg" style="background-image:url('{{ landing_asset('landing/images/2296b393-0309-42ba-b6cc-405569f55368.JPG') }}');"></div>
    <div class="nd-page-hero-overlay"></div>
    <div class="nd-page-hero-content">
        <div class="container-fluid px-4 px-lg-5">
            <div class="nd-breadcrumb">
                <a href="{{ $baseUrl }}">Home</a>
                <span>›</span>
                <span style="color:rgba(255,255,255,.75);">Services</span>
            </div>
            <p class="nd-page-hero-eyebrow">What We Offer</p>
            <h1 class="nd-page-hero-title">Our <span>Services</span></h1>
            <p class="nd-page-hero-sub">Personalised, evidence-based care for speech, language, hearing, and beyond.</p>
        </div>
    </div>
</div>


<!-- ── Feature Strip ─────────────────────────────────── -->
<section class="nd-section bg-gray-50" id="services">
    <div class="nd-section-inner">

        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:44px;" data-aos="fade-up">
            <span class="nd-section-label">Core Services</span>
            <h2 class="nd-section-title">Specialised <span class="nd-accent">Therapy</span> Services</h2>
            <p class="nd-section-text" style="margin-left:auto;margin-right:auto;max-width:560px;">
                From speech and language therapy to hearing evaluations and occupational support,
                personalised, evidence-based care for all ages.
            </p>
        </div>

        <?php
        $infographic_services = [
            ['title'=>'Speech Therapy',      'col'=>'nd-feature-col--teal',  'desc'=>'Comprehensive speech and language assessment and therapy for children and adults: helping you find your voice.', 'icon_d'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
            ['title'=>'Audiology Services',  'col'=>'nd-feature-col--yellow','desc'=>'Hearing evaluations, hearing-aid fittings, and auditory rehabilitation services for all age groups.', 'icon_d'=>'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3'],
            ['title'=>'Language Therapy',    'col'=>'nd-feature-col--red',   'desc'=>'Specialised language therapy for children and adults facing communication challenges or developmental delays.', 'icon_d'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['title'=>'Occupational Therapy','col'=>'nd-feature-col--navy',  'desc'=>'Supporting daily living skills, fine motor development, and sensory integration for meaningful independence.', 'icon_d'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ];
        ?>

        <div class="nd-feature-strip" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($infographic_services as $s): ?>
                <div class="nd-feature-col <?php echo $s['col']; ?>">
                    <svg class="nd-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?php echo $s['icon_d']; ?>"/>
                    </svg>
                    <h3 class="nd-feature-h3"><?php echo htmlspecialchars($s['title']); ?></h3>
                    <p class="nd-feature-text"><?php echo htmlspecialchars($s['desc']); ?></p>
                    <a href="{{ route('contact') }}" class="nd-feature-link">Book a Session</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ── Extended Services ─────────────────────────────── -->
<section class="nd-section bg-white" id="more-services">
    <div class="nd-section-inner">

        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:48px;" data-aos="fade-up">
            <span class="nd-section-label">Additional Support</span>
            <h2 class="nd-section-title">More Ways We <span class="nd-accent">Help</span></h2>
        </div>

        <?php
        $extServices = [
            ['title'=>'Special Education',     'icon_d'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color'=>'var(--primary-color)', 'desc'=>'Tailored educational support for children with learning difficulties, developmental delays, and special needs.'],
            ['title'=>'Early Intervention',    'icon_d'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color'=>'var(--green-color)', 'desc'=>'Early identification and intervention for infants and toddlers with developmental concerns: the earlier, the better.'],
            ['title'=>'Hearing Aid Fitting',   'icon_d'=>'M15.536 8.464a5 5 0 010 7.072M12 9.75a2.25 2.25 0 000 4.5m0-4.5v4.5M12 21.75v-3M5.25 12H3M8.03 6.22L6.27 4.46M15.97 6.22l1.76-1.76M8.03 17.78L6.27 19.54M15.97 17.78l1.76 1.76M21 12h-2.25', 'color'=>'#00A896', 'desc'=>'Professional hearing-aid selection, fitting, and follow-up care to ensure the best auditory experience.'],
            ['title'=>'Stuttering Therapy',    'icon_d'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color'=>'var(--primary-color)', 'desc'=>'Evidence-based techniques to reduce stuttering and build fluency, confidence, and clear communication.'],
            ['title'=>'Voice Therapy',         'icon_d'=>'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'color'=>'var(--green-color)', 'desc'=>'Therapeutic exercises for voice disorders, pitch and volume concerns, and vocal health rehabilitation.'],
            ['title'=>'Parent Counselling',    'icon_d'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color'=>'#00A896', 'desc'=>'Guidance and strategies for parents to support their child\'s communication development at home and in school.'],
        ];
        ?>
        <div class="row g-4" style="padding:0 1rem;">
            <?php foreach ($extServices as $i => $s): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($i % 3) * 80; ?>">
                <div style="background:#fdf4f8;border-radius:20px;padding:32px 26px;height:100%;border:1px solid rgba(223,85,137,.10);transition:transform .25s,box-shadow .25s;"
                     onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 36px rgba(0,0,0,.10)'"
                     onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="width:48px;height:48px;border-radius:12px;background:<?php echo $s['color']; ?>1a;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
                        <svg width="24" height="24" fill="none" stroke="<?php echo $s['color']; ?>" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?php echo $s['icon_d']; ?>"/>
                        </svg>
                    </div>
                    <h3 style="font-family:var(--heading-font);font-size:1.4rem;font-weight:400;color:var(--black-color);margin-bottom:10px;"><?php echo $s['title']; ?></h3>
                    <p style="color:var(--gray-color);font-size:.88rem;line-height:1.75;margin:0;"><?php echo $s['desc']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ── How Our Sessions Work ─────────────────────────── -->
<section class="nd-section bg-gray-50" id="process">
    <div class="nd-section-inner">
        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:44px;" data-aos="fade-up">
            <span class="nd-section-label">Our Process</span>
            <h2 class="nd-section-title">How Our <span class="nd-accent">Sessions</span> Work</h2>
        </div>

        <?php
        $steps = [
            ['num'=>1,'title'=>'Initial Assessment', 'desc'=>'Understanding your unique needs, communication goals, and medical background.'],
            ['num'=>2,'title'=>'Treatment Plan',      'desc'=>'A personalised, evidence-based approach designed around you and your family.'],
            ['num'=>3,'title'=>'Regular Sessions',    'desc'=>'Consistent therapy with real-time progress tracking and feedback.'],
            ['num'=>4,'title'=>'Ongoing Support',     'desc'=>'Continuous care, follow-ups, and family guidance beyond the clinic.'],
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

        <div style="text-align:center;margin-top:44px;" data-aos="fade-up" data-aos-delay="400">
            <a href="{{ route('contact') }}" class="nd-btn nd-btn-orange">
                Book a Session Today
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>


@include('landing.partials.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });
});
</script>
</body>
</html>
