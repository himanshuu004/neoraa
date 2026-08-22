<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// ── Collect & sanitise fields ─────────────────────────────
$name               = trim($_POST['name']               ?? '');
$mobile             = trim($_POST['mobile']             ?? '');
$email              = trim($_POST['email']              ?? '');
$city               = trim($_POST['city']               ?? '');
$applying_for       = trim($_POST['applying_for']       ?? '');
$qualification      = trim($_POST['qualification']      ?? '');
$college            = trim($_POST['college']            ?? '');
$year               = trim($_POST['year']               ?? '');
$experience_type    = trim($_POST['experience_type']    ?? '');
$experience_years   = trim($_POST['experience_years']   ?? '');
$current_place      = trim($_POST['current_place']      ?? '');
$areas_specialization = trim($_POST['areas_specialization'] ?? '');
$languages          = trim($_POST['languages']          ?? '');
$joining_time       = trim($_POST['joining_time']       ?? '');
$why_join_neora     = trim($_POST['why_join_neora']     ?? '');

// ── Validate required fields ──────────────────────────────
if ($name === '' || $mobile === '' || $email === '') {
    echo json_encode(['success' => false, 'message' => 'Name, mobile, and email are required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}
if (!preg_match('/^[0-9+\-\s]{7,15}$/', $mobile)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid mobile number.']);
    exit;
}

// ── File upload helper ────────────────────────────────────
function uploadDoc($fileKey, $prefix) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return [null, null];
    }
    $file     = $_FILES[$fileKey];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowed)) {
        return [null, 'Only PDF, DOC, DOCX, JPG, PNG files are accepted for ' . $fileKey];
    }
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return [null, 'File size for ' . $fileKey . ' exceeds 5 MB limit.'];
    }
    $uploadDir   = public_path('uploads/applications/';
    $relativeDir = 'uploads/applications/';
    if (!is_dir($uploadDir)) {
        if (!@mkdir($uploadDir, 0755, true)) {
            return [null, 'Could not create upload folder. Check server permissions.'];
        }
    }
    if (!is_writable($uploadDir)) {
        return [null, 'Upload folder is not writable. Check folder permissions.'];
    }
    $newName = $prefix . '_' . time() . '_' . uniqid() . '.' . $ext;
    $dest    = $uploadDir . $newName;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return [null, 'Failed to save uploaded file.'];
    }
    return [$relativeDir . $newName, null];
}

// Upload resume
[$resume_path, $resumeErr] = uploadDoc('resume', 'resume');
if ($resumeErr) {
    echo json_encode(['success' => false, 'message' => $resumeErr]);
    exit;
}

// Upload certificate (optional)
[$certificate_path, $certErr] = uploadDoc('certificate', 'cert');
if ($certErr) {
    // Clean up resume if already uploaded
    if ($resume_path && file_exists(public_path($resume_path)) {
        @unlink(public_path($resume_path);
    }
    echo json_encode(['success' => false, 'message' => $certErr]);
    exit;
}

// ── Insert into database ──────────────────────────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO hiring_applications
            (name, mobile, email, city, applying_for, qualification, college, year,
             experience_type, experience_years, current_place, areas_specialization,
             languages, joining_time, resume_path, certificate_path, why_join_neora, status)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?,
             ?, ?, ?, ?,
             ?, ?, ?, ?, ?, 'New')
    ");
    $stmt->execute([
        $name, $mobile, $email, $city ?: null, $applying_for ?: null,
        $qualification ?: null, $college ?: null, $year ?: null,
        $experience_type ?: null, $experience_years ?: null, $current_place ?: null,
        $areas_specialization ?: null, $languages ?: null, $joining_time ?: null,
        $resume_path, $certificate_path, $why_join_neora ?: null,
    ]);
    $id = (int) $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Your application has been submitted successfully!',
        'id'      => $id,
    ]);
} catch (Exception $e) {
    // Clean up uploaded files on DB failure
    if ($resume_path     && file_exists(public_path($resume_path))     @unlink(public_path($resume_path);
    if ($certificate_path && file_exists(public_path($certificate_path)) @unlink(public_path($certificate_path);
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
?>
