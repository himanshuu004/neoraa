<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    return;
}

$reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
if ($reviewId < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid review id']);
    return;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    return;
}

$file = $_FILES['photo'];
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
$allowedExtensions = ['jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG allowed']);
    return;
}

// Max dimensions and quality for compression
$maxWidth = 800;
$maxHeight = 800;
$jpegQuality = 82;
$pngCompression = 8;

$uploadDir = public_path('uploads/reviews').'/'.';
$relativeDir = 'uploads/reviews/';
if (!is_dir($uploadDir)) {
    if (!@mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Could not create upload folder. Check server permissions.']);
        return;
    }
}
if (!is_writable($uploadDir)) {
    echo json_encode(['success' => false, 'message' => 'Upload folder is not writable. Check folder permissions.']);
    return;
}

$tmpPath = $file['tmp_name'];

function compressAndSave($tmpPath, $destPath, $ext, $maxW, $maxH, $jpegQuality, $pngCompression) {
    if ($ext === 'jpg' || $ext === 'jpeg') {
        $src = @imagecreatefromjpeg($tmpPath);
    } elseif ($ext === 'png') {
        $src = @imagecreatefrompng($tmpPath);
    } else {
        return false;
    }
    if (!$src) {
        return false;
    }
    $w = imagesx($src);
    $h = imagesy($src);
    if ($w <= $maxW && $h <= $maxH) {
        $nw = $w;
        $nh = $h;
    } else {
        $r = min($maxW / $w, $maxH / $h);
        $nw = (int) round($w * $r);
        $nh = (int) round($h * $r);
    }
    $out = imagecreatetruecolor($nw, $nh);
    if (!$out) {
        imagedestroy($src);
        return false;
    }
    imagecopyresampled($out, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imagedestroy($src);
    $ok = false;
    if ($ext === 'jpg' || $ext === 'jpeg') {
        $ok = imagejpeg($out, $destPath, $jpegQuality);
    } elseif ($ext === 'png') {
        $ok = imagepng($out, $destPath, $pngCompression);
    }
    imagedestroy($out);
    return $ok;
}

$newFileName = 'review_' . $reviewId . '_' . time() . '.jpg';
$destPath = $uploadDir . $newFileName;
$saved = compressAndSave($tmpPath, $destPath, $ext, $maxWidth, $maxHeight, $jpegQuality, $pngCompression);

$dbPath = null;
$writtenPath = null;

if (!$saved) {
    $fallbackName = 'review_' . $reviewId . '_' . time() . '.' . $ext;
    $fallbackPath = $uploadDir . $fallbackName;
    if (move_uploaded_file($tmpPath, $fallbackPath)) {
        $dbPath = $relativeDir . $fallbackName;
        $writtenPath = $fallbackPath;
    } else {
        $err = error_get_last();
        $hint = (is_dir($uploadDir) && !is_writable($uploadDir)) ? ' Upload folder is not writable.'
            : (file_exists($tmpPath) ? '' : ' Temp file may be missing.');
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save image.' . $hint . (isset($err['message']) ? ' (' . $err['message'] . ')' : '')
        ]);
        return;
    }
} else {
    $dbPath = $relativeDir . $newFileName;
    $writtenPath = $destPath;
}

try {
    $stmt = $pdo->prepare("SELECT photo_path FROM reviews WHERE id = ?");
    $stmt->execute([$reviewId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $oldPath = $row['photo_path'] ?? null;
    if ($oldPath && file_exists(public_path($oldPath))) {
        @unlink(public_path($oldPath));
    }
    $stmt = $pdo->prepare("UPDATE reviews SET photo_path = ? WHERE id = ?");
    $stmt->execute([$dbPath, $reviewId]);
    echo json_encode(['success' => true, 'message' => 'Photo saved', 'photo_path' => $dbPath]);
} catch (Exception $e) {
    if ($writtenPath && file_exists($writtenPath)) {
        @unlink($writtenPath);
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
