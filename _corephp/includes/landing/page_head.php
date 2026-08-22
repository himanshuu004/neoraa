<?php
/**
 * Shared <head> for all public-facing landing pages.
 * Include AFTER require_once config/config.php.
 *
 * Expected variable before including:
 *   $pageTitle  (string) — e.g. 'About Us'
 *   $pageDesc   (string, optional) — meta description
 */
$pageTitle = isset($pageTitle) ? $pageTitle . ' – NEORA Speech Therapy & Audiology Clinic' : 'NEORA Speech Therapy & Audiology Clinic';
$pageDesc  = isset($pageDesc)  ? $pageDesc  : 'NEORA – Leading speech therapy and audiology clinic in Dehradun. Expert care for speech, language, hearing, and occupational therapy.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- NEORA design system -->
    <link rel="stylesheet" href="<?php echo defined('BASE_URL') ? htmlspecialchars(BASE_URL) : '/'; ?>assets/css/neora-redesign.css">

    <!-- JS (defer so inline DOMContentLoaded scripts work) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
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

        /* ── Page hero banner (used on every inner page) ── */
        .nd-page-hero {
            position: relative;
            height: 340px;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
        }
        .nd-page-hero-bg {
            position: absolute; inset: 0;
            background-size: cover;
            background-position: center 35%;
            transition: transform 9s ease;
        }
        .nd-page-hero:hover .nd-page-hero-bg { transform: scale(1.04); }
        .nd-page-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(
                to top,
                rgba(26,26,46,.90) 0%,
                rgba(223,85,137,.28) 55%,
                rgba(0,0,0,.22) 100%
            );
        }
        .nd-page-hero-content {
            position: relative; z-index: 2;
            padding: 0 0 48px;
            width: 100%;
        }
        .nd-page-hero-eyebrow {
            font-size: .68rem; font-weight: 700;
            letter-spacing: 3px; text-transform: uppercase;
            color: rgba(255,255,255,.55); margin-bottom: 8px;
        }
        .nd-page-hero-title {
            font-family: var(--heading-font);
            font-size: clamp(2rem, 5vw, 3.4rem);
            font-weight: 400; color: #fff;
            margin: 0; line-height: 1.1;
        }
        .nd-page-hero-title span { color: #ffcde0; }
        .nd-page-hero-sub {
            color: rgba(255,255,255,.70);
            font-size: .9rem; margin-top: 10px;
            max-width: 520px;
        }
        /* breadcrumb */
        .nd-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: .75rem; color: rgba(255,255,255,.5);
            margin-bottom: 12px;
        }
        .nd-breadcrumb a { color: rgba(255,255,255,.6); text-decoration: none; }
        .nd-breadcrumb a:hover { color: #ffcde0; }
        .nd-breadcrumb span { color: rgba(255,255,255,.35); }
    </style>
</head>
<body>
