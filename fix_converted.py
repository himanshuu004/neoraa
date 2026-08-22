#!/usr/bin/env python3
from pathlib import Path

ROOT = Path("/Applications/XAMPP/xamppfiles/htdocs/neoranewbie")


def strip_before_doctype(path: Path, prefix: str = "") -> None:
    text = path.read_text(encoding="utf-8")
    idx = text.find("<!DOCTYPE html>")
    if idx == -1:
        return
    path.write_text(prefix + text[idx:], encoding="utf-8")
    print("stripped", path.relative_to(ROOT))


def replace_in(path: Path, mapping: list[tuple[str, str]]) -> None:
    text = path.read_text(encoding="utf-8")
    orig = text
    for old, new in mapping:
        text = text.replace(old, new)
    if text != orig:
        path.write_text(text, encoding="utf-8")
        print("updated", path.relative_to(ROOT))


strip_before_doctype(
    ROOT / "resources/views/auth/login.blade.php",
    """@php
$error = $error ?? '';
$saved_username = $saved_username ?? '';
$saved_user_type = $saved_user_type ?? 'admin';
@endphp
""",
)

strip_before_doctype(
    ROOT / "resources/views/landing/apply.blade.php",
    """@php
$error = $error ?? '';
$generated_by = $generatedBy ?? ($generated_by ?? null);
@endphp
""",
)

# Asset paths
for rel in [
    "resources/views/admin/dashboard.blade.php",
    "resources/views/admin/bookings.blade.php",
    "resources/views/admin/reviews.blade.php",
    "resources/views/admin/application.blade.php",
    "resources/views/admin/my_session.blade.php",
    "resources/views/therapist/dashboard.blade.php",
    "resources/views/trainee/dashboard.blade.php",
    "resources/views/coordinator/dashboard.blade.php",
]:
    replace_in(ROOT / rel, [
        ('href="../assets/css/style.css"', 'href="{{ $baseUrl }}assets/css/style.css"'),
        ('src="../assets/js/admin.js"', 'src="{{ $baseUrl }}assets/js/admin.js"'),
        ('src="../assets/js/timetable.js"', 'src="{{ $baseUrl }}assets/js/timetable.js"'),
        ('src="../assets/js/therapist.js"', 'src="{{ $baseUrl }}assets/js/therapist.js"'),
    ])

replace_in(ROOT / "resources/views/admin/dashboard.blade.php", [
    ("<?php include 'includes/create_therapist_modal.php'; ?>", "@include('admin.partials.create_therapist_modal')"),
    ("<?php include 'includes/edit_therapist_modal.php'; ?>", "@include('admin.partials.edit_therapist_modal')"),
    ("<?php include 'includes/create_session_modal.php'; ?>", "@include('admin.partials.create_session_modal')"),
    ("<?php include 'includes/edit_session_modal.php'; ?>", "@include('admin.partials.edit_session_modal')"),
    ("<?php include 'includes/add_kid_modal.php'; ?>", "@include('admin.partials.add_kid_modal')"),
    ("<?php include 'includes/edit_kid_modal.php'; ?>", "@include('admin.partials.edit_kid_modal')"),
])

replace_in(ROOT / "resources/views/therapist/dashboard.blade.php", [
    ("include_once '../admin/includes/dynamic_timetable_grid.php';", "echo view('admin.partials.dynamic_timetable_grid', compact('pdo', 'is_editable') + ['baseUrl' => $baseUrl, 'siteName' => $siteName])->render();"),
])

replace_in(ROOT / "resources/views/admin/application.blade.php", [
    ("<?php @include($isCoordinator ? 'coordinator.partials.navbar' : 'admin.partials.navbar') ?>", "@include($isCoordinator ? 'coordinator.partials.navbar' : 'admin.partials.navbar')"),
])

# Legacy upload dirs
legacy_fixes = [
    (ROOT / "app/Legacy/admin/api/upload_profile_picture.php", [
        ("$uploadDir = '../../uploads/admin_profiles/';", "$uploadDir = public_path('uploads/admin_profiles').'/';"),
        ("file_exists('../../' . $oldImagePath)", "file_exists(public_path($oldImagePath))"),
        ("unlink('../../' . $oldImagePath)", "unlink(public_path($oldImagePath))"),
    ]),
    (ROOT / "app/Legacy/therapist/api/upload_profile_picture.php", [
        ("$uploadDir = '../../uploads/therapist_profiles/';", "$uploadDir = public_path('uploads/therapist_profiles').'/';"),
        ("file_exists('../../' . $oldImagePath)", "file_exists(public_path($oldImagePath))"),
        ("unlink('../../' . $oldImagePath)", "unlink(public_path($oldImagePath))"),
    ]),
    (ROOT / "app/Legacy/trainee/api/upload_profile_picture.php", [
        ("$uploadDir = '../uploads/trainee_profiles/';", "$uploadDir = public_path('uploads/trainee_profiles').'/';"),
    ]),
    (ROOT / "app/Legacy/trainee/api/submit_attendance.php", [
        ("$uploadDir = dirname(dirname(__DIR__)) . '/uploads/trainee_sessions/';", "$uploadDir = public_path('uploads/trainee_sessions').'/';"),
    ]),
    (ROOT / "app/Legacy/admin/api/upload_review_photo.php", [
        ("file_exists(__DIR__ . '/../../' . $oldPath)", "file_exists(public_path($oldPath))"),
        ("@unlink(__DIR__ . '/../../' . $oldPath)", "@unlink(public_path($oldPath))"),
    ]),
    (ROOT / "app/Legacy/admin/api/delete_review.php", [
        ("$path = __DIR__ . '/../../' . $row['photo_path'];", "$path = public_path($row['photo_path']);"),
    ]),
    (ROOT / "app/Legacy/admin/api/test_kids_setup.php", [
        ("$uploadDir = '../../uploads/kids/';", "$uploadDir = public_path('uploads/kids').'/';"),
    ]),
]
for path, mapping in legacy_fixes:
    if path.exists():
        replace_in(path, mapping)

print("done")
