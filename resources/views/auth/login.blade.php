@php
$error = $error ?? session('login_error', '');
if (! $error && isset($errors) && $errors->any()) {
    $error = $errors->first();
}
$saved_username = old('username', $saved_username ?? '');
$saved_user_type = old('user_type', $saved_user_type ?? 'admin');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="referrer" content="no-referrer">
    <title>Staff Sign In – {{ $siteName }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Google Fonts: same as main page -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Main design system -->
    <link rel="stylesheet" href="{{ $baseUrl }}assets/css/neora-redesign.css">

    <style>
        /* ── Login page override ─────────────────────────────── */
        :root {
            --login-panel-w: 480px;
        }

        html, body { height: 100%; }

        body.nd-login-body {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            background: #1a1a2e;
            font-family: var(--body-font);
        }

        /* ── Left brand panel ─────────────────────────── */
        .nd-login-brand {
            flex: 1 1 55%;
            position: relative;
            overflow: hidden;
            display: none;           /* hidden on mobile */
        }
        @media (min-width: 992px) { .nd-login-brand { display: flex; } }

        .nd-login-brand-img {
            position: absolute; inset: 0;
            background: url('{{ landing_asset('landing/sliders/494bd119-204d-4c5f-9b19-29fb0ae98677.JPG') }}') center center / cover no-repeat;
            transition: transform 12s ease;
        }
        .nd-login-brand:hover .nd-login-brand-img { transform: scale(1.04); }

        .nd-login-brand-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(135deg,
                rgba(26,26,46,.88) 0%,
                rgba(223,85,137,.45) 60%,
                rgba(161,196,74,.30) 100%);
        }

        .nd-login-brand-content {
            position: relative; z-index: 2;
            display: flex; flex-direction: column;
            justify-content: space-between;
            width: 100%; padding: 56px 52px;
            color: #fff;
        }

        .nd-login-brand-logo {
            height: 68px; width: auto; object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .nd-login-brand-headline {
            font-family: var(--heading-font);
            font-size: 3rem; font-weight: 400;
            line-height: 1.15; color: #fff;
            margin-bottom: 18px;
        }
        .nd-login-brand-headline em {
            font-style: normal;
            color: #ffcde0;
        }

        .nd-login-brand-sub {
            font-size: .95rem; line-height: 1.7;
            color: rgba(255,255,255,.8); max-width: 360px;
        }

        .nd-login-brand-badges {
            display: flex; flex-wrap: wrap; gap: 10px; margin-top: 32px;
        }
        .nd-login-brand-badge {
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 100px; padding: 6px 16px;
            font-size: .75rem; font-weight: 600; letter-spacing: .5px;
            color: #fff; backdrop-filter: blur(8px);
        }

        .nd-login-brand-footer {
            font-size: .75rem; color: rgba(255,255,255,.5);
        }

        /* ── Right form panel ─────────────────────────── */
        .nd-login-form-panel {
            flex: 0 0 100%;
            display: flex; align-items: center; justify-content: center;
            background: #fdfdfd;
            padding: 40px 24px;
        }
        @media (min-width: 992px) {
            .nd-login-form-panel { flex: 0 0 var(--login-panel-w); }
        }

        .nd-login-form-inner {
            width: 100%; max-width: 400px;
        }

        .nd-login-form-inner .login-logo-mobile {
            display: block; height: 56px; margin: 0 auto 24px;
            object-fit: contain;
        }
        @media (min-width: 992px) { .login-logo-mobile { display: none !important; } }

        .nd-login-form-inner .nd-login-eyebrow {
            font-size: .72rem; font-weight: 700; letter-spacing: 2.5px;
            text-transform: uppercase; color: var(--primary-color);
            margin-bottom: 6px;
        }

        .nd-login-form-inner h2 {
            font-family: var(--heading-font);
            font-size: 2.1rem; font-weight: 400;
            color: var(--black-color); margin-bottom: 28px;
            line-height: 1.2;
        }
        .nd-login-form-inner h2 span { color: var(--primary-color); }

        /* form fields */
        .nd-field { margin-bottom: 18px; }
        .nd-field label {
            display: block; font-size: .78rem; font-weight: 600;
            color: #4a4a5a; margin-bottom: 6px; letter-spacing: .3px;
        }
        .nd-field-wrap { position: relative; }
        .nd-field-wrap i.nd-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #aaa; font-size: .85rem; pointer-events: none;
        }
        .nd-field-wrap .nd-field-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #e0e0ec;
            border-radius: 12px;
            background: #f8f5fb;
            font-family: var(--body-font); font-size: .9rem;
            color: var(--black-color);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            appearance: none; -webkit-appearance: none;
        }
        .nd-field-wrap .nd-field-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(223,85,137,.12);
            background: #fff;
        }
        .nd-field-wrap .nd-pw-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; padding: 4px 6px;
            color: #aaa; cursor: pointer; border-radius: 6px;
            transition: color .2s, background .2s;
        }
        .nd-field-wrap .nd-pw-toggle:hover {
            color: var(--primary-color);
            background: rgba(223,85,137,.08);
        }

        /* remember-me row */
        .nd-remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px; margin-top: -4px;
        }
        .nd-remember-row input[type="checkbox"] {
            width: 16px; height: 16px; accent-color: var(--primary-color); cursor: pointer;
        }
        .nd-remember-row label {
            font-size: .82rem; color: #666; cursor: pointer;
        }

        /* submit button */
        .nd-login-btn {
            width: 100%;
            padding: 13px;
            border: none; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #c03674 100%);
            color: #fff;
            font-family: var(--body-font); font-size: .95rem; font-weight: 600;
            letter-spacing: .5px;
            cursor: pointer;
            box-shadow: 0 6px 22px rgba(223,85,137,.35);
            transition: transform .2s, box-shadow .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .nd-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(223,85,137,.45);
        }
        .nd-login-btn:active { transform: scale(.98); }

        /* error alert */
        .nd-login-alert {
            background: #fff0f3; border: 1px solid #ffc0cb;
            border-left: 4px solid #e05577;
            border-radius: 10px; padding: 12px 16px;
            font-size: .83rem; color: #9b2335;
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 20px;
        }

        /* footer note */
        .nd-login-footnote {
            text-align: center; margin-top: 28px;
            font-size: .75rem; color: #aaa;
        }
        .nd-hp {
            position: absolute;
            left: -10000px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        /* green accent bar at top of form panel */
        .nd-login-form-panel::before {
            content: '';
            position: fixed; top: 0; right: 0;
            width: var(--login-panel-w); height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--green-color) 100%);
            z-index: 10;
        }
        @media (max-width: 991px) {
            .nd-login-form-panel::before { width: 100%; }
        }

        /* select caret */
        .nd-field-wrap .nd-caret {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            color: #aaa; font-size: .75rem; pointer-events: none;
        }
    </style>
</head>
<body class="nd-login-body">

    <!-- ── Left brand panel ───────────────────── -->
    <div class="nd-login-brand" data-aos="fade-right" data-aos-duration="900">
        <div class="nd-login-brand-img"></div>
        <div class="nd-login-brand-overlay"></div>
        <div class="nd-login-brand-content">
            <!-- top: logo -->
            <div>
                <img src="{{ $baseUrl }}uploads/logo.png"
                     alt="NEORA"
                     class="nd-login-brand-logo">
            </div>

            <!-- middle: headline -->
            <div>
                <p style="font-size:.7rem;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,.55);margin-bottom:14px;">
                    Secure Staff Portal
                </p>
                <h2 class="nd-login-brand-headline">
                    Transforming Lives<br>
                    Through <em>Better</em><br>
                    Communication.
                </h2>
                <p class="nd-login-brand-sub">
                    NEORA Speech Therapy &amp; Audiology Clinic, evidence-based,
                    goal-oriented care for children and adults.
                </p>
                <div class="nd-login-brand-badges">
                    <span class="nd-login-brand-badge">BASLP Certified</span>
                    <span class="nd-login-brand-badge">Speech Therapy</span>
                    <span class="nd-login-brand-badge">Audiology</span>
                    <span class="nd-login-brand-badge">Early Intervention</span>
                </div>
            </div>

            <!-- bottom: copyright -->
            <div class="nd-login-brand-footer">
                &copy; <?php echo date('Y'); ?> NEORA Therapy &amp; Audiology Clinic. All rights reserved.
            </div>
        </div>
    </div>

    <!-- ── Right form panel ────────────────────── -->
    <div class="nd-login-form-panel">
        <div class="nd-login-form-inner" data-aos="fade-up" data-aos-duration="700">

            <!-- Mobile-only logo -->
            <img src="{{ $baseUrl }}uploads/logo.png"
                 alt="NEORA"
                 class="login-logo-mobile">

            <p class="nd-login-eyebrow">Staff Portal</p>
            <h2>Welcome<br><span>Back</span></h2>

            <?php if ($error): ?>
            <div class="nd-login-alert">
                <i class="fas fa-exclamation-circle" style="margin-top:1px;flex-shrink:0;color:#e05577;"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="{{ route('login.store') }}" id="loginForm" autocomplete="on">
                @csrf
                <div class="nd-hp" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

                <!-- User type -->
                <div class="nd-field">
                    <label for="user_type">User Type</label>
                    <div class="nd-field-wrap">
                        <i class="fas fa-user-tag nd-icon"></i>
                        <select name="user_type" id="user_type" required class="nd-field-input" style="padding-right:36px;">
                            <option value="admin"       <?php echo ($saved_user_type === 'admin'       || (isset($_POST['user_type']) && $_POST['user_type'] === 'admin'))       ? 'selected' : ''; ?>>Admin</option>
                            <option value="coordinator" <?php echo ($saved_user_type === 'coordinator' || (isset($_POST['user_type']) && $_POST['user_type'] === 'coordinator')) ? 'selected' : ''; ?>>Coordinator</option>
                            <option value="therapist"   <?php echo ($saved_user_type === 'therapist'   || (isset($_POST['user_type']) && $_POST['user_type'] === 'therapist'))   ? 'selected' : ''; ?>>Therapist</option>
                            <option value="trainee"     <?php echo ($saved_user_type === 'trainee'     || (isset($_POST['user_type']) && $_POST['user_type'] === 'trainee'))     ? 'selected' : ''; ?>>Trainee</option>
                        </select>
                        <i class="fas fa-chevron-down nd-caret"></i>
                    </div>
                </div>

                <!-- Username -->
                <div class="nd-field">
                    <label for="username">Username</label>
                    <div class="nd-field-wrap">
                        <i class="fas fa-user nd-icon"></i>
                        <input type="text" name="username" id="username"
                               value="<?php echo htmlspecialchars($saved_username); ?>"
                               required autofocus
                               maxlength="64"
                               autocomplete="username"
                               placeholder="Enter your username"
                               class="nd-field-input">
                    </div>
                </div>

                <!-- Password -->
                <div class="nd-field">
                    <label for="password">Password</label>
                    <div class="nd-field-wrap">
                        <i class="fas fa-lock nd-icon"></i>
                        <input type="password" name="password" id="password"
                               required
                               maxlength="255"
                               autocomplete="current-password"
                               placeholder="Enter your password"
                               class="nd-field-input" style="padding-right:44px;">
                        <button type="button" id="togglePassword" aria-label="Show password" class="nd-pw-toggle">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="nd-remember-row">
                    <input type="checkbox" id="remember_me" name="remember_me" value="1"
                           <?php echo isset($_COOKIE['remember_username']) ? 'checked' : ''; ?>>
                    <label for="remember_me">Remember username on this device</label>
                </div>

                <button type="submit" class="nd-login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <p class="nd-login-footnote">
                <i class="fas fa-shield-alt" style="color:var(--green-color);margin-right:4px;"></i>
                Secure access for authorized staff only.
                <br>
                <a href="{{ $baseUrl }}" style="color:var(--primary-color);text-decoration:none;font-weight:600;">
                    &larr; Back to website
                </a>
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // AOS
        if (typeof AOS !== 'undefined') AOS.init({ duration: 800, once: true });

        // Toggle password visibility
        $('#togglePassword').on('click', function () {
            var $pw   = $('#password');
            var $icon = $('#toggleIcon');
            if ($pw.attr('type') === 'password') {
                $pw.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                $(this).attr('aria-label', 'Hide password');
            } else {
                $pw.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                $(this).attr('aria-label', 'Show password');
            }
        });
    });
    </script>
</body>
</html>
