<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

// Ensure tables exist

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'trainee') {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profile_picture'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    return;
}

$trainee_id = $_SESSION['user_id'];
$file = $_FILES['profile_picture'];

// Validate file
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
$maxSize = 5 * 1024 * 1024; // 5MB

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, and PNG are allowed.']);
    return;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
    return;
}

try {
    // Create uploads directory if it doesn't exist
    $uploadDir = public_path('uploads/trainee_profiles').'/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'trainee_' . $trainee_id . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    $relativePath = 'uploads/trainee_profiles/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE trainee_profile SET profile_image = ? WHERE user_id = ?");
        $stmt->execute([$relativePath, $trainee_id]);
        
        // If profile doesn't exist, create it
        if ($stmt->rowCount() === 0) {
            $stmt = $pdo->prepare("INSERT INTO trainee_profile (user_id, profile_image) VALUES (?, ?)");
            $stmt->execute([$trainee_id, $relativePath]);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Profile picture uploaded successfully',
            'image_path' => $relativePath
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
