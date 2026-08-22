#!/usr/bin/env python3
"""Convert core PHP views/APIs into Laravel Blade + Legacy API files."""
from __future__ import annotations

import os
import re
from pathlib import Path

ROOT = Path("/Applications/XAMPP/xamppfiles/htdocs/neoranewbie")
SRC = ROOT / "_corephp"
VIEWS = ROOT / "resources" / "views"
LEGACY = ROOT / "app" / "Legacy"

REQUIRE_RE = re.compile(
    r"require(_once)?\s+[^;]*?(config\.php|database\.php|session\.php|reviews_schema\.php|trainee_schema\.php|careers_schema\.php)['\"]?\s*;\s*",
    re.I,
)
AUTH_RE = re.compile(
    r"(requireAdmin\(\)|requireTherapist\(\)|requireTrainee\(\)|requireLogin\(\)|requireAdminOrCoordinator\(\))\s*;\s*",
)


def ensure_parent(path: Path) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)


def strip_bootstrap(text: str) -> str:
    text = REQUIRE_RE.sub("", text)
    text = AUTH_RE.sub("", text)
    text = re.sub(
        r"if\s*\(\s*!isCoordinator\(\)\)\s*\{.*?exit\(\);\s*\}",
        "",
        text,
        flags=re.S,
    )
    return text


def replace_urls(text: str) -> str:
    replacements = [
        ("<?php echo htmlspecialchars(BASE_URL); ?>", "{{ $baseUrl }}"),
        ("<?php echo htmlspecialchars($navBase); ?>", "{{ $baseUrl }}"),
        ("<?php echo htmlspecialchars($footerBase); ?>", "{{ $baseUrl }}"),
        ("<?php echo BASE_URL; ?>", "{{ $baseUrl }}"),
        ("<?php echo SITE_NAME; ?>", "{{ $siteName }}"),
        ("<?= BASE_URL ?>", "{{ $baseUrl }}"),
        ("<?= htmlspecialchars(BASE_URL) ?>", "{{ $baseUrl }}"),
    ]
    for old, new in replacements:
        text = text.replace(old, new)

    text = text.replace("htmlspecialchars(BASE_URL . '", "htmlspecialchars($baseUrl . '")
    text = text.replace("htmlspecialchars(BASE_URL . \"", "htmlspecialchars($baseUrl . \"")
    text = text.replace("BASE_URL . '", "$baseUrl . '")
    text = text.replace("BASE_URL . \"", "$baseUrl . \"")
    text = text.replace("defined('BASE_URL') ? BASE_URL : '/'", "$baseUrl")
    text = text.replace("defined('BASE_URL') ? htmlspecialchars(BASE_URL) : '/'", "$baseUrl")
    return text


def replace_includes(text: str, kind: str) -> str:
    mapping = {
        "landing": [
            (r"<\?php\s+include\s+__DIR__\s*\.\s*'/includes/landing/navbar\.php'\s*;\s*\?>", "@include('landing.partials.navbar')"),
            (r"<\?php\s+include\s+__DIR__\s*\.\s*'/includes/landing/footer\.php'\s*;\s*\?>", "@include('landing.partials.footer')"),
            (r"<\?php\s+include\s+__DIR__\s*\.\s*'/includes/landing/hero\.php'\s*;\s*\?>", "@include('landing.partials.hero')"),
            (r"<\?php\s+include\s+__DIR__\s*\.\s*'/includes/landing/page_head\.php'\s*;\s*\?>", "@include('landing.partials.page_head')"),
            (r"<\?php\s+include\s+__DIR__\s*\.\s*'/includes/landing/section\.php'\s*;\s*\?>", "@include('landing.partials.section')"),
            (r"<\?php\s+include\s+__DIR__\s*\.\s*'/includes/landing/slider\.php'\s*;\s*\?>", "@include('landing.partials.slider')"),
            (r"include\s+__DIR__\s*\.\s*'/includes/landing/navbar\.php'\s*;", "@include('landing.partials.navbar')"),
            (r"include\s+__DIR__\s*\.\s*'/includes/landing/footer\.php'\s*;", "@include('landing.partials.footer')"),
            (r"include\s+__DIR__\s*\.\s*'/includes/landing/hero\.php'\s*;", "@include('landing.partials.hero')"),
            (r"include\s+__DIR__\s*\.\s*'/includes/landing/page_head\.php'\s*;", "@include('landing.partials.page_head')"),
            (r"include\s+__DIR__\s*\.\s*'/includes/landing/section\.php'\s*;", "@include('landing.partials.section')"),
        ],
        "admin": [
            (r"<\?php\s+include\s+'includes/navbar\.php'\s*;\s*\?>", "@include('admin.partials.navbar')"),
            (r"include\s+'includes/navbar\.php'\s*;", "@include('admin.partials.navbar')"),
            (r"include\s+\$navbar_include\s*;", "@include($isCoordinator ? 'coordinator.partials.navbar' : 'admin.partials.navbar')"),
        ],
        "therapist": [
            (r"<\?php\s+include\s+'includes/navbar\.php'\s*;\s*\?>", "@include('therapist.partials.navbar')"),
            (r"include\s+'includes/navbar\.php'\s*;", "@include('therapist.partials.navbar')"),
        ],
        "trainee": [
            (r"<\?php\s+include\s+'includes/navbar\.php'\s*;\s*\?>", "@include('trainee.partials.navbar')"),
            (r"include\s+'includes/navbar\.php'\s*;", "@include('trainee.partials.navbar')"),
        ],
        "coordinator": [
            (r"<\?php\s+include\s+'includes/navbar\.php'\s*;\s*\?>", "@include('coordinator.partials.navbar')"),
            (r"include\s+'includes/navbar\.php'\s*;", "@include('coordinator.partials.navbar')"),
        ],
    }
    for pattern, repl in mapping.get(kind, []):
        text = re.sub(pattern, repl, text)
    return text


def replace_file_exists(text: str) -> str:
    text = text.replace("file_exists(__DIR__ . '/' . $photoPath)", "file_exists(public_path($photoPath))")
    text = text.replace("file_exists(__DIR__ . '/' . $photo_path)", "file_exists(public_path($photo_path))")
    text = text.replace("file_exists('../' . $profileImage)", "file_exists(public_path($profileImage))")
    text = text.replace("file_exists('../' . $profile_image)", "file_exists(public_path($profile_image))")
    text = re.sub(
        r"file_exists\(\s*__DIR__\s*\.\s*'/' \.\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\)",
        r"file_exists(public_path($\1))",
        text,
    )
    return text


def replace_active_nav(text: str) -> str:
    text = text.replace(
        "basename($_SERVER['PHP_SELF']) === 'index.php'",
        "request()->is('/')"
    )
    text = text.replace(
        "basename($_SERVER['PHP_SELF']) === 'about.php'",
        "request()->is('about', 'about.php')"
    )
    text = text.replace(
        "basename($_SERVER['PHP_SELF']) === 'services.php'",
        "request()->is('services', 'services.php')"
    )
    text = text.replace(
        "basename($_SERVER['PHP_SELF']) === 'gallery.php'",
        "request()->is('gallery', 'gallery.php')"
    )
    text = text.replace(
        "basename($_SERVER['PHP_SELF']) === 'testimonials.php'",
        "request()->is('testimonials', 'testimonials.php')"
    )
    text = text.replace(
        "basename($_SERVER['PHP_SELF']) === 'contact.php'",
        "request()->is('contact', 'contact.php')"
    )
    text = text.replace(
        "basename($_SERVER['PHP_SELF']) === 'careers.php'",
        "request()->is('careers', 'careers.php')"
    )
    return text


def convert_view(src: Path, dest: Path, kind: str) -> None:
    text = src.read_text(encoding="utf-8", errors="replace")
    text = strip_bootstrap(text)
    text = replace_urls(text)
    text = replace_includes(text, kind)
    text = replace_file_exists(text)
    text = replace_active_nav(text)
    # Keep $pdo working in dashboards that still query inline.
    if kind in {"admin", "therapist", "trainee", "coordinator"}:
        prefix = """@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
"""
        text = prefix + text
    ensure_parent(dest)
    dest.write_text(text, encoding="utf-8")
    print(f"view: {dest.relative_to(ROOT)}")


def convert_legacy_api(src: Path, dest: Path) -> None:
    text = src.read_text(encoding="utf-8", errors="replace")
    text = strip_bootstrap(text)
    # Upload paths: original files live 1 or 2 levels below project root.
    text = text.replace("__DIR__ . '/../../uploads/", "public_path('uploads/")
    text = text.replace('__DIR__ . "/../../uploads/', 'public_path("uploads/')
    text = text.replace("__DIR__ . '/../uploads/", "public_path('uploads/")
    text = text.replace('__DIR__ . "/../uploads/', 'public_path("uploads/')
    text = text.replace("__DIR__ . '/../' . $", "public_path($")
    text = text.replace("rtrim(__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads'", "public_path('uploads')")
    # After public_path('uploads/reviews/') the original concatenated more — keep a trailing concat-safe form
    text = text.replace("public_path('uploads/reviews/'", "public_path('uploads/reviews').'/'.'")
    # Fix over-aggressive replace if it created broken quotes — handled case-by-case below
    text = text.replace("BASE_URL", "(defined('BASE_URL') ? BASE_URL : rtrim(url('/'), '/').'/')")
    header = """<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

"""
    # Drop opening <?php if present
    text = re.sub(r"^\s*<\?php\s*", "", text, count=1)
    ensure_parent(dest)
    dest.write_text(header + text, encoding="utf-8")
    print(f"api:  {dest.relative_to(ROOT)}")


def main() -> None:
    views = [
        (SRC / "includes/landing/navbar.php", VIEWS / "landing/partials/navbar.blade.php", "landing"),
        (SRC / "includes/landing/footer.php", VIEWS / "landing/partials/footer.blade.php", "landing"),
        (SRC / "includes/landing/hero.php", VIEWS / "landing/partials/hero.blade.php", "landing"),
        (SRC / "includes/landing/page_head.php", VIEWS / "landing/partials/page_head.blade.php", "landing"),
        (SRC / "includes/landing/section.php", VIEWS / "landing/partials/section.blade.php", "landing"),
        (SRC / "includes/landing/slider.php", VIEWS / "landing/partials/slider.blade.php", "landing"),
        (SRC / "index.php", VIEWS / "landing/home.blade.php", "landing"),
        (SRC / "about.php", VIEWS / "landing/about.blade.php", "landing"),
        (SRC / "services.php", VIEWS / "landing/services.blade.php", "landing"),
        (SRC / "gallery.php", VIEWS / "landing/gallery.blade.php", "landing"),
        (SRC / "contact.php", VIEWS / "landing/contact.blade.php", "landing"),
        (SRC / "careers.php", VIEWS / "landing/careers.blade.php", "landing"),
        (SRC / "testimonials.php", VIEWS / "landing/testimonials.blade.php", "landing"),
        (SRC / "reviews.php", VIEWS / "landing/reviews.blade.php", "landing"),
        (SRC / "apply.php", VIEWS / "landing/apply.blade.php", "landing"),
        (SRC / "apply-confirmation.php", VIEWS / "landing/apply-confirmation.blade.php", "landing"),
        (SRC / "login.php", VIEWS / "auth/login.blade.php", "landing"),
        (SRC / "admin/includes/navbar.php", VIEWS / "admin/partials/navbar.blade.php", "admin"),
        (SRC / "admin/dashboard.php", VIEWS / "admin/dashboard.blade.php", "admin"),
        (SRC / "admin/bookings.php", VIEWS / "admin/bookings.blade.php", "admin"),
        (SRC / "admin/reviews.php", VIEWS / "admin/reviews.blade.php", "admin"),
        (SRC / "admin/application.php", VIEWS / "admin/application.blade.php", "admin"),
        (SRC / "admin/my_session.php", VIEWS / "admin/my_session.blade.php", "admin"),
        (SRC / "admin/includes/timetable_grid.php", VIEWS / "admin/partials/timetable_grid.blade.php", "admin"),
        (SRC / "admin/includes/dynamic_timetable_grid.php", VIEWS / "admin/partials/dynamic_timetable_grid.blade.php", "admin"),
        (SRC / "admin/includes/create_session_modal.php", VIEWS / "admin/partials/create_session_modal.blade.php", "admin"),
        (SRC / "admin/includes/create_therapist_modal.php", VIEWS / "admin/partials/create_therapist_modal.blade.php", "admin"),
        (SRC / "admin/includes/edit_therapist_modal.php", VIEWS / "admin/partials/edit_therapist_modal.blade.php", "admin"),
        (SRC / "admin/includes/edit_session_modal.php", VIEWS / "admin/partials/edit_session_modal.blade.php", "admin"),
        (SRC / "admin/includes/add_kid_modal.php", VIEWS / "admin/partials/add_kid_modal.blade.php", "admin"),
        (SRC / "admin/includes/edit_kid_modal.php", VIEWS / "admin/partials/edit_kid_modal.blade.php", "admin"),
        (SRC / "therapist/includes/navbar.php", VIEWS / "therapist/partials/navbar.blade.php", "therapist"),
        (SRC / "therapist/dashboard.php", VIEWS / "therapist/dashboard.blade.php", "therapist"),
        (SRC / "trainee/includes/navbar.php", VIEWS / "trainee/partials/navbar.blade.php", "trainee"),
        (SRC / "trainee/dashboard.php", VIEWS / "trainee/dashboard.blade.php", "trainee"),
        (SRC / "coordinator/includes/navbar.php", VIEWS / "coordinator/partials/navbar.blade.php", "coordinator"),
        (SRC / "coordinator/dashboard.php", VIEWS / "coordinator/dashboard.blade.php", "coordinator"),
    ]
    for src, dest, kind in views:
        if src.exists():
            convert_view(src, dest, kind)
        else:
            print(f"MISSING view src: {src}")

    api_roots = [
        (SRC / "admin/api", LEGACY / "admin/api"),
        (SRC / "therapist/api", LEGACY / "therapist/api"),
        (SRC / "trainee/api", LEGACY / "trainee/api"),
        (SRC / "api", LEGACY / "api"),
    ]
    for src_dir, dest_dir in api_roots:
        if not src_dir.exists():
            continue
        for src in src_dir.glob("*.php"):
            convert_legacy_api(src, dest_dir / src.name)


if __name__ == "__main__":
    main()
