<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/careers_schema.php';
$pageTitle = 'Careers';
$pageDesc  = 'Join the NEORA team — we are looking for passionate speech therapists, audiologists, and special educators to grow with us.';
include __DIR__ . '/includes/landing/page_head.php';
?>

<style>
/* ── Careers page extras ──────────────────────────── */

/* Job-opening cards */
.nd-job-card {
    background: #fff;
    border-radius: 20px;
    padding: 30px 28px;
    border: 1.5px solid rgba(223,85,137,.10);
    box-shadow: 0 4px 18px rgba(0,0,0,.06);
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    cursor: default;
    height: 100%;
}
.nd-job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 38px rgba(223,85,137,.12);
    border-color: rgba(223,85,137,.30);
}
.nd-job-tag {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 100px;
    font-size: .7rem; font-weight: 700; letter-spacing: .8px;
    text-transform: uppercase; margin-bottom: 14px;
}
.nd-job-tag-full  { background: rgba(161,196,74,.15); color: #5a7a1a; }
.nd-job-tag-part  { background: rgba(223,85,137,.12); color: #b83065; }
.nd-job-tag-intern{ background: rgba(0,168,150,.12);  color: #006b60; }
.nd-job-title { font-family: var(--heading-font); font-size:1.55rem; font-weight:400; color:var(--black-color); margin-bottom:8px; }
.nd-job-meta  { display:flex; flex-wrap:wrap; gap:14px; margin-bottom:14px; }
.nd-job-meta span { font-size:.78rem; color:var(--gray-color); display:flex; align-items:center; gap:5px; }
.nd-job-desc  { font-size:.87rem; color:var(--gray-color); line-height:1.75; margin-bottom:20px; }
.nd-job-apply-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 22px; border-radius: 100px;
    background: var(--primary-color); color: #fff;
    font-family: var(--body-font); font-size: .83rem; font-weight: 600;
    border: none; cursor: pointer;
    transition: background .2s, transform .2s, box-shadow .2s;
    text-decoration: none;
}
.nd-job-apply-btn:hover {
    background: #c03674; color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(223,85,137,.35);
}

/* Multi-step form */
.nd-career-form-wrap {
    background: #fff;
    border-radius: 24px;
    padding: 48px 52px;
    box-shadow: 0 8px 40px rgba(0,0,0,.10);
    border-top: 5px solid var(--primary-color);
}
@media (max-width:767px) { .nd-career-form-wrap { padding: 28px 20px; } }

/* Step indicator */
.nd-steps-indicator {
    display: flex; align-items: center; justify-content: center;
    gap: 0; margin-bottom: 40px; flex-wrap: nowrap;
}
.nd-step-dot {
    display: flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 50%;
    font-size: .78rem; font-weight: 700;
    background: #f0e8f2; color: #aaa;
    border: 2px solid #e0d0e8;
    transition: all .3s; flex-shrink: 0; position: relative; z-index: 1;
}
.nd-step-dot.active  { background: var(--primary-color); color: #fff; border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(223,85,137,.18); }
.nd-step-dot.done    { background: var(--green-color);   color: #fff; border-color: var(--green-color); }
.nd-step-label { font-size: .68rem; font-weight: 600; color: #aaa; text-align: center; margin-top: 6px; white-space: nowrap; }
.nd-step-label.active { color: var(--primary-color); }
.nd-step-label.done   { color: var(--green-color); }
.nd-step-connector {
    flex: 1; height: 2px; background: #e0d0e8;
    min-width: 20px; max-width: 80px;
    transition: background .3s;
}
.nd-step-connector.done { background: var(--green-color); }
.nd-step-wrap { display: flex; flex-direction: column; align-items: center; }

/* Form step panels */
.nd-form-step { display: none; }
.nd-form-step.active { display: block; }

/* Form fields (reuse main design system) */
.nd-career-form-wrap .nd-form-label { margin-bottom: 6px; display: block; font-size: .78rem; font-weight: 600; color: #4a4a5a; letter-spacing: .3px; }
.nd-career-form-wrap .nd-form-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid #e0e0ec; border-radius: 12px;
    background: #f8f5fb; font-family: var(--body-font);
    font-size: .9rem; color: var(--black-color);
    outline: none; transition: border-color .2s, box-shadow .2s;
    appearance: none; -webkit-appearance: none;
}
.nd-career-form-wrap .nd-form-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(223,85,137,.12);
    background: #fff;
}
.nd-career-form-wrap .nd-form-input::placeholder { color: #bbb; }
.nd-career-form-wrap textarea.nd-form-input { resize: vertical; min-height: 90px; }
.nd-form-row { display: grid; gap: 18px; margin-bottom: 18px; }
.nd-form-row-2 { grid-template-columns: 1fr 1fr; }
.nd-form-row-3 { grid-template-columns: 1fr 1fr 1fr; }
@media (max-width:640px) {
    .nd-form-row-2, .nd-form-row-3 { grid-template-columns: 1fr; }
}
.nd-form-group { margin-bottom: 18px; }

/* Step nav buttons */
.nd-step-nav { display: flex; gap: 12px; justify-content: space-between; margin-top: 28px; }
.nd-btn-prev {
    padding: 11px 28px; border-radius: 100px;
    background: #f0e8f2; border: none; color: #777;
    font-family: var(--body-font); font-size: .88rem; font-weight: 600;
    cursor: pointer; transition: background .2s;
}
.nd-btn-prev:hover { background: #e0d0e8; }
.nd-btn-next, .nd-btn-submit {
    padding: 11px 32px; border-radius: 100px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #c03674 100%);
    border: none; color: #fff;
    font-family: var(--body-font); font-size: .88rem; font-weight: 600;
    cursor: pointer; box-shadow: 0 4px 14px rgba(223,85,137,.30);
    transition: transform .2s, box-shadow .2s;
    display: inline-flex; align-items: center; gap: 6px;
}
.nd-btn-next:hover, .nd-btn-submit:hover {
    transform: translateY(-2px); box-shadow: 0 8px 22px rgba(223,85,137,.40);
}

/* File upload field */
.nd-file-label {
    display: flex; align-items: center; gap: 10px;
    border: 1.5px dashed rgba(223,85,137,.40); border-radius: 12px;
    padding: 14px 18px; background: #fdf4f8; cursor: pointer;
    transition: border-color .2s, background .2s;
}
.nd-file-label:hover { border-color: var(--primary-color); background: #fce8f2; }
.nd-file-input { display: none; }
.nd-file-name { font-size: .8rem; color: var(--gray-color); flex: 1; }

/* Radio group */
.nd-radio-group { display: flex; gap: 12px; flex-wrap: wrap; }
.nd-radio-label {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 18px; border-radius: 100px;
    border: 1.5px solid #e0d0e8; background: #f8f5fb;
    font-size: .85rem; cursor: pointer;
    transition: all .2s;
}
.nd-radio-label:has(input:checked) { background: var(--primary-color); border-color: var(--primary-color); color: #fff; }
.nd-radio-label input { display: none; }

/* ── Success Modal ────────────────────────────────── */
.nd-success-backdrop {
    display: none; position: fixed; inset: 0; z-index: 1060;
    background: rgba(10,10,20,.70); backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.nd-success-backdrop.show { display: flex; }
.nd-success-modal {
    background: #fff; border-radius: 28px; padding: 52px 40px;
    max-width: 480px; width: 92%; text-align: center;
    box-shadow: 0 24px 72px rgba(0,0,0,.20);
    animation: ndModalIn .4s cubic-bezier(.34,1.56,.64,1);
}
@keyframes ndModalIn {
    from { opacity: 0; transform: scale(.85) translateY(20px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.nd-success-circle {
    width: 88px; height: 88px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--green-color));
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px; box-shadow: 0 8px 28px rgba(223,85,137,.35);
}
.nd-success-modal h3 {
    font-family: var(--heading-font); font-size: 2rem; font-weight: 400;
    color: var(--black-color); margin-bottom: 10px;
}
.nd-success-modal p { font-size: .9rem; color: var(--gray-color); line-height: 1.7; margin-bottom: 8px; }
.nd-success-ref { font-size: .78rem; color: #bbb; margin-bottom: 28px; }
.nd-success-ref strong { color: var(--primary-color); }
</style>

<?php include __DIR__ . '/includes/landing/navbar.php'; ?>

<!-- ── Page Hero ─────────────────────────────────────── -->
<div class="nd-page-hero" data-aos="fade-in">
    <div class="nd-page-hero-bg" style="background-image:url('public/landing/images/d3263e16-976f-4a9f-8842-a2e3b75dadf3.JPG');"></div>
    <div class="nd-page-hero-overlay"></div>
    <div class="nd-page-hero-content">
        <div class="container-fluid px-4 px-lg-5">
            <div class="nd-breadcrumb">
                <a href="<?php echo BASE_URL; ?>">Home</a>
                <span>›</span>
                <span style="color:rgba(255,255,255,.75);">Careers</span>
            </div>
            <p class="nd-page-hero-eyebrow">Join Our Team</p>
            <h1 class="nd-page-hero-title">Grow With <span>NEORA</span></h1>
            <p class="nd-page-hero-sub">
                We are looking for passionate, purpose-driven professionals who want to make a real difference in people's lives.
            </p>
        </div>
    </div>
</div>


<!-- ── Why Join NEORA ─────────────────────────────────── -->
<section class="nd-section bg-white" id="why-join">
    <div class="nd-section-inner">
        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:48px;" data-aos="fade-up">
            <span class="nd-section-label">Life at NEORA</span>
            <h2 class="nd-section-title">Why Work <span class="nd-accent">With Us</span></h2>
            <p class="nd-section-text" style="margin:0 auto;max-width:520px;">
                At NEORA, we do more than just therapy — we build futures. Our culture is driven by compassion,
                continuous learning, and genuine teamwork.
            </p>
        </div>

        <?php
        $perks = [
            ['icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z','title'=>'Purpose-Driven Work','desc'=>'Every day you help real people — children and adults — reach communication milestones that change their lives.','color'=>'var(--primary-color)'],
            ['icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','title'=>'Continuous Learning','desc'=>'Regular workshops, case discussions, and mentorship to keep you at the cutting edge of therapy practice.','color'=>'var(--green-color)'],
            ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','title'=>'Collaborative Team','desc'=>'Work alongside certified BASLP professionals in a warm, supportive, and inclusive environment.','color'=>'#00A896'],
            ['icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','title'=>'Professional Growth','desc'=>'Structured career progression and recognition for your contributions — grow with NEORA as we grow.','color'=>'var(--primary-color)'],
            ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Flexible Schedules','desc'=>'We respect work-life balance. Flexible timings and a supportive management that understands your needs.','color'=>'var(--green-color)'],
            ['icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4','title'=>'Modern Facility','desc'=>'State-of-the-art clinic in Dehradun with all the equipment, tools, and space you need to deliver your best.','color'=>'#00A896'],
        ];
        ?>
        <div class="row g-4" style="padding:0 1rem;">
            <?php foreach ($perks as $i => $p): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($i % 3) * 80; ?>">
                <div style="display:flex;gap:18px;align-items:flex-start;">
                    <div style="flex-shrink:0;width:48px;height:48px;border-radius:14px;background:<?php echo $p['color']; ?>18;display:flex;align-items:center;justify-content:center;">
                        <svg width="22" height="22" fill="none" stroke="<?php echo $p['color']; ?>" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?php echo $p['icon']; ?>"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.9rem;color:var(--black-color);margin-bottom:4px;"><?php echo $p['title']; ?></div>
                        <div style="font-size:.82rem;color:var(--gray-color);line-height:1.65;"><?php echo $p['desc']; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ── Open Positions ─────────────────────────────────── -->
<section class="nd-section bg-gray-50" id="positions">
    <div class="nd-section-inner">
        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:48px;" data-aos="fade-up">
            <span class="nd-section-label">Open Roles</span>
            <h2 class="nd-section-title">Current <span class="nd-accent">Openings</span></h2>
            <p class="nd-section-text" style="margin:0 auto;max-width:480px;">
                Don't see your role below? We welcome open applications from passionate professionals anytime.
            </p>
        </div>

        <?php
        $jobs = [
            [
                'title'  => 'Speech-Language Therapist',
                'type'   => 'Full-Time', 'tag' => 'nd-job-tag-full',
                'loc'    => 'Dehradun, Uttarakhand',
                'exp'    => '0 – 3 Years',
                'qual'   => 'BASLP / MASLP',
                'desc'   => 'Conduct assessments and deliver individual therapy sessions for speech, language, fluency, and voice disorders across paediatric and adult populations.',
                'skills' => ['BASLP/MASLP Certified', 'Paediatric Experience', 'Parent Counselling'],
            ],
            [
                'title'  => 'Audiologist',
                'type'   => 'Full-Time', 'tag' => 'nd-job-tag-full',
                'loc'    => 'Dehradun, Uttarakhand',
                'exp'    => '0 – 2 Years',
                'qual'   => 'B.Sc Audiology or BASLP',
                'desc'   => 'Perform hearing assessments, audiograms, hearing-aid fittings, and auditory rehabilitation for patients of all ages.',
                'skills' => ['Audiometric Testing', 'Hearing Aid Fitting', 'Tympanometry'],
            ],
            [
                'title'  => 'Special Educator',
                'type'   => 'Full-Time', 'tag' => 'nd-job-tag-full',
                'loc'    => 'Dehradun, Uttarakhand',
                'exp'    => '1 – 4 Years',
                'qual'   => 'B.Ed Special Education / RCI Certified',
                'desc'   => 'Plan and deliver individualised education programmes for children with learning disabilities, autism, and developmental delays.',
                'skills' => ['IEP Development', 'Autism Spectrum', 'Behavioural Support'],
            ],
            [
                'title'  => 'Occupational Therapist',
                'type'   => 'Full-Time', 'tag' => 'nd-job-tag-full',
                'loc'    => 'Dehradun, Uttarakhand',
                'exp'    => '0 – 3 Years',
                'qual'   => 'B.Sc / M.Sc Occupational Therapy',
                'desc'   => 'Support clients in developing fine motor skills, sensory integration, ADL, and cognitive skills for greater independence.',
                'skills' => ['Sensory Integration', 'Fine Motor', 'ADL Training'],
            ],
            [
                'title'  => 'Clinical Intern – Speech & Audiology',
                'type'   => 'Trainee', 'tag' => 'nd-job-tag-intern',
                'loc'    => 'Dehradun, Uttarakhand',
                'exp'    => 'Fresher Welcome',
                'qual'   => 'BASLP (Final Year)',
                'desc'   => 'Hands-on clinical internship under the mentorship of BASLP Priyanka Rawat. Learn real-world therapy techniques and patient management.',
                'skills' => ['Observation', 'Documentation', 'Patient Interaction'],
            ],
            [
                'title'  => 'Front Desk / Clinic Coordinator',
                'type'   => 'Part-Time', 'tag' => 'nd-job-tag-part',
                'loc'    => 'Dehradun, Uttarakhand',
                'exp'    => '0 – 2 Years',
                'qual'   => 'Graduate (Any Stream)',
                'desc'   => 'Manage appointments, patient records, billing, and day-to-day clinic administration. Excellent communication skills required.',
                'skills' => ['Communication', 'Organisation', 'Computer Literacy'],
            ],
        ];
        ?>
        <div class="row g-4" style="padding:0 1rem;">
            <?php foreach ($jobs as $i => $job): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($i % 3) * 70; ?>">
                <div class="nd-job-card">
                    <span class="nd-job-tag <?php echo $job['tag']; ?>"><?php echo $job['type']; ?></span>
                    <div class="nd-job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                    <div class="nd-job-meta">
                        <span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <?php echo htmlspecialchars($job['loc']); ?>
                        </span>
                        <span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <?php echo htmlspecialchars($job['exp']); ?>
                        </span>
                        <span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            <?php echo htmlspecialchars($job['qual']); ?>
                        </span>
                    </div>
                    <p class="nd-job-desc"><?php echo htmlspecialchars($job['desc']); ?></p>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:20px;">
                        <?php foreach ($job['skills'] as $sk): ?>
                        <span style="background:#f0e8f2;color:#8a2060;font-size:.68rem;font-weight:600;padding:3px 10px;border-radius:100px;letter-spacing:.4px;">
                            <?php echo htmlspecialchars($sk); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <button class="nd-job-apply-btn" onclick="applyForJob('<?php echo htmlspecialchars(addslashes($job['title'])); ?>')">
                        Apply Now
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ── Application Form ───────────────────────────────── -->
<section class="nd-section bg-white" id="apply">
    <div class="nd-section-inner">
        <div class="nd-heading-group nd-heading-group--center nd-heading-padded" style="margin-bottom:48px;" data-aos="fade-up">
            <span class="nd-section-label">Apply Now</span>
            <h2 class="nd-section-title">Send Your <span class="nd-accent">Application</span></h2>
            <p class="nd-section-text" style="margin:0 auto;max-width:480px;">
                Fill in your details below. We review every application personally and get back within 3–5 working days.
            </p>
        </div>

        <div style="max-width:820px;margin:0 auto;" data-aos="fade-up" data-aos-delay="100">
            <div class="nd-career-form-wrap">

                <!-- Step indicator -->
                <div class="nd-steps-indicator" id="stepsIndicator">
                    <div class="nd-step-wrap">
                        <div class="nd-step-dot active" id="dot1">1</div>
                        <div class="nd-step-label active" id="lbl1">Personal</div>
                    </div>
                    <div class="nd-step-connector" id="conn1"></div>
                    <div class="nd-step-wrap">
                        <div class="nd-step-dot" id="dot2">2</div>
                        <div class="nd-step-label" id="lbl2">Education</div>
                    </div>
                    <div class="nd-step-connector" id="conn2"></div>
                    <div class="nd-step-wrap">
                        <div class="nd-step-dot" id="dot3">3</div>
                        <div class="nd-step-label" id="lbl3">Experience</div>
                    </div>
                    <div class="nd-step-connector" id="conn3"></div>
                    <div class="nd-step-wrap">
                        <div class="nd-step-dot" id="dot4">4</div>
                        <div class="nd-step-label" id="lbl4">Documents</div>
                    </div>
                </div>

                <form id="careerForm" enctype="multipart/form-data" novalidate>

                    <!-- ─ STEP 1: Personal Info ─ -->
                    <div class="nd-form-step active" id="step1">
                        <h4 style="font-family:var(--heading-font);font-size:1.5rem;font-weight:400;margin-bottom:22px;color:var(--black-color);">
                            <span style="color:var(--primary-color);">01.</span> Personal Information
                        </h4>

                        <div class="nd-form-row nd-form-row-2">
                            <div>
                                <label class="nd-form-label">Full Name <span style="color:#E84855;">*</span></label>
                                <input type="text" name="name" id="f_name" required placeholder="Your full name" class="nd-form-input">
                            </div>
                            <div>
                                <label class="nd-form-label">Mobile Number <span style="color:#E84855;">*</span></label>
                                <input type="tel" name="mobile" id="f_mobile" required placeholder="e.g. 98765 43210" class="nd-form-input">
                            </div>
                        </div>

                        <div class="nd-form-row nd-form-row-2">
                            <div>
                                <label class="nd-form-label">Email Address <span style="color:#E84855;">*</span></label>
                                <input type="email" name="email" id="f_email" required placeholder="you@example.com" class="nd-form-input">
                            </div>
                            <div>
                                <label class="nd-form-label">City / Location</label>
                                <input type="text" name="city" placeholder="e.g. Dehradun" class="nd-form-input">
                            </div>
                        </div>

                        <div class="nd-form-group">
                            <label class="nd-form-label">Position Applying For <span style="color:#E84855;">*</span></label>
                            <select name="applying_for" id="f_applying_for" required class="nd-form-input" style="cursor:pointer;">
                                <option value="">— Select a position —</option>
                                <option>Speech-Language Therapist</option>
                                <option>Audiologist</option>
                                <option>Special Educator</option>
                                <option>Occupational Therapist</option>
                                <option>Clinical Intern – Speech &amp; Audiology</option>
                                <option>Front Desk / Clinic Coordinator</option>
                                <option>Other / Open Application</option>
                            </select>
                        </div>

                        <div class="nd-step-nav">
                            <span></span>
                            <button type="button" class="nd-btn-next" onclick="goStep(1,2)">
                                Next: Education
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- ─ STEP 2: Education ─ -->
                    <div class="nd-form-step" id="step2">
                        <h4 style="font-family:var(--heading-font);font-size:1.5rem;font-weight:400;margin-bottom:22px;color:var(--black-color);">
                            <span style="color:var(--primary-color);">02.</span> Educational Background
                        </h4>

                        <div class="nd-form-group">
                            <label class="nd-form-label">Highest Qualification <span style="color:#E84855;">*</span></label>
                            <select name="qualification" id="f_qualification" required class="nd-form-input" style="cursor:pointer;">
                                <option value="">— Select qualification —</option>
                                <option>BASLP (Bachelor of Audiology and Speech-Language Pathology)</option>
                                <option>MASLP (Master of Audiology and Speech-Language Pathology)</option>
                                <option>B.Sc Audiology</option>
                                <option>M.Sc Audiology</option>
                                <option>B.Ed Special Education</option>
                                <option>M.Ed Special Education</option>
                                <option>B.Sc Occupational Therapy</option>
                                <option>M.Sc Occupational Therapy</option>
                                <option>Graduate (Other)</option>
                                <option>Post-Graduate (Other)</option>
                            </select>
                        </div>

                        <div class="nd-form-row nd-form-row-2">
                            <div>
                                <label class="nd-form-label">College / University <span style="color:#E84855;">*</span></label>
                                <input type="text" name="college" id="f_college" required placeholder="Name of your institution" class="nd-form-input">
                            </div>
                            <div>
                                <label class="nd-form-label">Year of Passing / Expected</label>
                                <input type="text" name="year" placeholder="e.g. 2024 or 2025 (expected)" class="nd-form-input">
                            </div>
                        </div>

                        <div class="nd-form-group">
                            <label class="nd-form-label">Languages Known</label>
                            <input type="text" name="languages" placeholder="e.g. Hindi, English, Garhwali" class="nd-form-input">
                        </div>

                        <div class="nd-step-nav">
                            <button type="button" class="nd-btn-prev" onclick="goStep(2,1)">← Back</button>
                            <button type="button" class="nd-btn-next" onclick="goStep(2,3)">
                                Next: Experience
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- ─ STEP 3: Experience ─ -->
                    <div class="nd-form-step" id="step3">
                        <h4 style="font-family:var(--heading-font);font-size:1.5rem;font-weight:400;margin-bottom:22px;color:var(--black-color);">
                            <span style="color:var(--primary-color);">03.</span> Work Experience
                        </h4>

                        <div class="nd-form-group">
                            <label class="nd-form-label">Experience Type <span style="color:#E84855;">*</span></label>
                            <div class="nd-radio-group">
                                <label class="nd-radio-label"><input type="radio" name="experience_type" id="f_exp_type_fresher" value="Fresher" required> Fresher</label>
                                <label class="nd-radio-label"><input type="radio" name="experience_type" value="Experienced"> Experienced</label>
                                <label class="nd-radio-label"><input type="radio" name="experience_type" value="Internship"> Intern / Trainee</label>
                            </div>
                        </div>

                        <div class="nd-form-row nd-form-row-2" id="expDetailsRow">
                            <div>
                                <label class="nd-form-label">Years of Experience</label>
                                <select name="experience_years" class="nd-form-input" style="cursor:pointer;">
                                    <option value="">— Select —</option>
                                    <option>Less than 1 year</option>
                                    <option>1 year</option>
                                    <option>2 years</option>
                                    <option>3 years</option>
                                    <option>4 years</option>
                                    <option>5+ years</option>
                                </select>
                            </div>
                            <div>
                                <label class="nd-form-label">Current / Last Workplace</label>
                                <input type="text" name="current_place" placeholder="Clinic / Hospital / School name" class="nd-form-input">
                            </div>
                        </div>

                        <div class="nd-form-group">
                            <label class="nd-form-label">Areas of Specialisation</label>
                            <input type="text" name="areas_specialization" placeholder="e.g. Autism, Stuttering, Hearing Aids, Paediatrics" class="nd-form-input">
                        </div>

                        <div class="nd-form-row nd-form-row-2">
                            <div>
                                <label class="nd-form-label">Available to Join</label>
                                <select name="joining_time" class="nd-form-input" style="cursor:pointer;">
                                    <option value="">— Select —</option>
                                    <option>Immediately</option>
                                    <option>Within 2 weeks</option>
                                    <option>Within 1 month</option>
                                    <option>Within 2 months</option>
                                    <option>More than 2 months</option>
                                </select>
                            </div>
                        </div>

                        <div class="nd-form-group">
                            <label class="nd-form-label">Why do you want to join NEORA?</label>
                            <textarea name="why_join_neora" rows="4" placeholder="Tell us what drives you and why NEORA is the right fit for you…" class="nd-form-input"></textarea>
                        </div>

                        <div class="nd-step-nav">
                            <button type="button" class="nd-btn-prev" onclick="goStep(3,2)">← Back</button>
                            <button type="button" class="nd-btn-next" onclick="goStep(3,4)">
                                Next: Documents
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- ─ STEP 4: Documents & Submit ─ -->
                    <div class="nd-form-step" id="step4">
                        <h4 style="font-family:var(--heading-font);font-size:1.5rem;font-weight:400;margin-bottom:22px;color:var(--black-color);">
                            <span style="color:var(--primary-color);">04.</span> Documents &amp; Submit
                        </h4>

                        <div class="nd-form-group">
                            <label class="nd-form-label">
                                Resume / CV <span style="color:#E84855;">*</span>
                                <span style="font-weight:400;color:#bbb;">&nbsp;PDF, DOC, DOCX — max 5 MB</span>
                            </label>
                            <label class="nd-file-label" for="f_resume">
                                <svg width="22" height="22" fill="none" stroke="var(--primary-color)" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span class="nd-file-name" id="resumeName">Click to upload your resume</span>
                            </label>
                            <input type="file" name="resume" id="f_resume" class="nd-file-input" accept=".pdf,.doc,.docx">
                        </div>

                        <div class="nd-form-group">
                            <label class="nd-form-label">
                                Degree / Registration Certificate
                                <span style="font-weight:400;color:#bbb;">&nbsp;(optional) — PDF, JPG, PNG — max 5 MB</span>
                            </label>
                            <label class="nd-file-label" for="f_certificate">
                                <svg width="22" height="22" fill="none" stroke="var(--green-color)" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span class="nd-file-name" id="certName">Click to upload your degree / certificate</span>
                            </label>
                            <input type="file" name="certificate" id="f_certificate" class="nd-file-input" accept=".pdf,.jpg,.jpeg,.png">
                        </div>

                        <!-- Summary box -->
                        <div id="appSummaryBox" style="background:#fdf4f8;border-radius:14px;padding:20px 22px;border:1px solid rgba(223,85,137,.15);margin-bottom:20px;font-size:.85rem;color:var(--gray-color);line-height:1.9;display:none;">
                            <p style="font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--primary-color);margin-bottom:10px;">Application Summary</p>
                            <div id="summaryContent"></div>
                        </div>

                        <div id="appFormError" style="display:none;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:.85rem;margin-bottom:16px;"></div>

                        <div class="nd-step-nav">
                            <button type="button" class="nd-btn-prev" onclick="goStep(4,3)">← Back</button>
                            <button type="submit" class="nd-btn-submit" id="submitAppBtn">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span id="submitAppText">Submit Application</span>
                                <span id="submitAppLoading" style="display:none;">Submitting…</span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>


<?php include __DIR__ . '/includes/landing/footer.php'; ?>


<!-- ── Success Popup ──────────────────────────────────── -->
<div class="nd-success-backdrop" id="ndSuccessBackdrop">
    <div class="nd-success-modal" role="dialog" aria-modal="true" aria-labelledby="successTitle">
        <div class="nd-success-circle">
            <svg width="42" height="42" fill="none" stroke="#fff" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 id="successTitle">Application Submitted!</h3>
        <p>Thank you for applying to <strong>NEORA Therapy &amp; Audiology Clinic</strong>.</p>
        <p>We have received your application and will review it carefully. If your profile matches our requirements, we'll be in touch within <strong>3–5 working days</strong>.</p>
        <p class="nd-success-ref" id="successRef"></p>
        <button onclick="closeSuccess()" class="nd-btn nd-btn-orange" style="width:100%;justify-content:center;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Great, Thank You!
        </button>
        <a href="<?php echo BASE_URL; ?>" style="display:block;text-align:center;margin-top:14px;font-size:.8rem;color:var(--gray-color);text-decoration:none;">
            ← Back to Home
        </a>
    </div>
</div>


<script>
var baseUrl = '<?php echo addslashes(BASE_URL); ?>';

// ── Scroll to apply form and set position ──────────────
function applyForJob(title) {
    var select = document.querySelector('[name="applying_for"]');
    if (select) {
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].text.indexOf(title) !== -1) {
                select.value = select.options[i].value;
                break;
            }
        }
    }
    var section = document.getElementById('apply');
    if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Step navigation ───────────────────────────────────
function validateStep(stepNum) {
    var step = document.getElementById('step' + stepNum);
    var required = step.querySelectorAll('[required]');
    var valid = true;
    required.forEach(function(el) {
        el.style.borderColor = '';
        if (!el.value.trim()) {
            el.style.borderColor = '#e05577';
            el.style.boxShadow   = '0 0 0 3px rgba(224,85,119,.15)';
            if (valid) el.focus();
            valid = false;
        }
    });
    return valid;
}

function goStep(from, to) {
    if (to > from && !validateStep(from)) return;

    document.getElementById('step' + from).classList.remove('active');
    document.getElementById('step' + to).classList.add('active');

    // Update indicators
    for (var i = 1; i <= 4; i++) {
        var dot = document.getElementById('dot' + i);
        var lbl = document.getElementById('lbl' + i);
        dot.classList.remove('active','done');
        lbl.classList.remove('active','done');
        if (i < to)      { dot.classList.add('done');   lbl.classList.add('done');   }
        else if (i === to){ dot.classList.add('active'); lbl.classList.add('active'); }
    }
    // connectors
    for (var c = 1; c <= 3; c++) {
        var conn = document.getElementById('conn' + c);
        if (conn) conn.classList.toggle('done', c < to);
    }

    // On step 4, build summary
    if (to === 4) buildSummary();

    document.getElementById('step' + to).scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function buildSummary() {
    var f = document.getElementById('careerForm');
    var data = {
        'Name':        f.querySelector('[name="name"]').value,
        'Mobile':      f.querySelector('[name="mobile"]').value,
        'Email':       f.querySelector('[name="email"]').value,
        'City':        f.querySelector('[name="city"]').value,
        'Applying For':f.querySelector('[name="applying_for"]').value,
        'Qualification':f.querySelector('[name="qualification"]').value,
        'College':     f.querySelector('[name="college"]').value,
        'Experience':  (function(){var r=f.querySelector('[name="experience_type"]:checked');return r?r.value:''})(),
    };
    var lines = Object.entries(data)
        .filter(function(kv){ return kv[1]; })
        .map(function(kv){
            return '<span style="font-weight:600;color:var(--black-color);">' + kv[0] + ':</span> ' + kv[1];
        }).join('<br>');
    var box = document.getElementById('appSummaryBox');
    document.getElementById('summaryContent').innerHTML = lines;
    box.style.display = lines ? 'block' : 'none';
}

// ── File name display ────────────────────────────────
document.getElementById('f_resume').addEventListener('change', function() {
    document.getElementById('resumeName').textContent = this.files[0] ? this.files[0].name : 'Click to upload your resume';
});
document.getElementById('f_certificate').addEventListener('change', function() {
    document.getElementById('certName').textContent = this.files[0] ? this.files[0].name : 'Click to upload your degree / certificate';
});

// ── Form submission ──────────────────────────────────
document.getElementById('careerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    var resumeFile = document.getElementById('f_resume').files[0];
    if (!resumeFile) {
        showFormError('Please upload your resume before submitting.');
        return;
    }

    var errEl   = document.getElementById('appFormError');
    var btn     = document.getElementById('submitAppBtn');
    var btnText = document.getElementById('submitAppText');
    var btnLoad = document.getElementById('submitAppLoading');

    errEl.style.display = 'none';
    btn.disabled        = true;
    btnText.style.display = 'none';
    btnLoad.style.display = 'inline';

    try {
        var res    = await fetch(baseUrl + 'api/submit_application.php', { method:'POST', body: new FormData(this) });
        var result = await res.json();

        if (result.success) {
            var refEl = document.getElementById('successRef');
            if (refEl && result.id) refEl.innerHTML = 'Your Application ID: <strong>#' + result.id + '</strong>';
            document.getElementById('ndSuccessBackdrop').classList.add('show');
            document.body.style.overflow = 'hidden';
            this.reset();
            document.getElementById('resumeName').textContent = 'Click to upload your resume';
            document.getElementById('certName').textContent   = 'Click to upload your degree / certificate';
            document.getElementById('appSummaryBox').style.display = 'none';
            // Reset to step 1
            goStep(parseInt(document.querySelector('.nd-form-step.active').id.replace('step','')) || 4, 1);
        } else {
            showFormError(result.message || 'Submission failed. Please try again.');
            btn.disabled = false;
            btnText.style.display = 'inline';
            btnLoad.style.display = 'none';
        }
    } catch (err) {
        showFormError('An error occurred. Please check your connection and try again.');
        btn.disabled = false;
        btnText.style.display = 'inline';
        btnLoad.style.display = 'none';
    }
});

function showFormError(msg) {
    var el = document.getElementById('appFormError');
    el.textContent = msg;
    el.style.display = 'block';
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── Close success modal ──────────────────────────────
function closeSuccess() {
    document.getElementById('ndSuccessBackdrop').classList.remove('show');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSuccess();
});

// ── AOS ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });
});
</script>
</body>
</html>
