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

$text = trim($_POST['text'] ?? '');
$author = trim($_POST['author'] ?? '');
$location = trim($_POST['location'] ?? '');
$photo_path = null;

// Validate required fields
if ($text === '' || $author === '') {
    echo json_encode(['success' => false, 'message' => 'Name and review content are required']);
    return;
}

// Handle photo upload if provided
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['photo'];
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG images are allowed']);
        return;
    }
    
    // Max file size: 5MB
    $maxFileSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxFileSize) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
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
    
    // Function to compress and save image
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
    
    $newFileName = 'review_' . time() . '_' . uniqid() . '.jpg';
    $destPath = $uploadDir . $newFileName;
    $saved = compressAndSave($tmpPath, $destPath, $ext, $maxWidth, $maxHeight, $jpegQuality, $pngCompression);
    
    if (!$saved) {
        // Fallback: save original file
        $fallbackName = 'review_' . time() . '_' . uniqid() . '.' . $ext;
        $fallbackPath = $uploadDir . $fallbackName;
        if (move_uploaded_file($tmpPath, $fallbackPath)) {
            $photo_path = $relativeDir . $fallbackName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save image']);
            return;
        }
    } else {
        $photo_path = $relativeDir . $newFileName;
    }
}

try {
    // Insert review into database
    $stmt = $pdo->prepare("INSERT INTO reviews (`text`, author, `location`, photo_path, display_order) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$text, $author, $location ?: null, $photo_path, 0]);
    $id = (int) $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Thank you for your review! It has been submitted successfully.',
        'id' => $id
    ]);
} catch (Exception $e) {
    // If database insert fails, delete uploaded photo if it exists
    if ($photo_path && file_exists(public_path($photo_path)) {
        @unlink(public_path($photo_path);
    }
    echo json_encode(['success' => false, 'message' => 'Failed to save review. Please try again.']);
}
?>
