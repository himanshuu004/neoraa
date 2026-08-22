<?php
require_once '../../config/config.php';
requireTherapist();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$therapistId = $_SESSION['user_id'];

// Check if file was uploaded
if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error occurred']);
    exit();
}

$file = $_FILES['profile_picture'];
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
$allowedExtensions = ['jpg', 'jpeg', 'png'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Validate file type
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($fileExtension, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, and PNG images are allowed']);
    exit();
}

// Additional MIME type check (if available)
$fileMimeType = '';
if (function_exists('mime_content_type')) {
    $fileMimeType = mime_content_type($file['tmp_name']);
} elseif (function_exists('finfo_file')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileMimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
}

if ($fileMimeType && !in_array($fileMimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, and PNG images are allowed']);
    exit();
}

// Validate file size
if ($file['size'] > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
    exit();
}

try {
    // Get old profile image path to delete it later
    $stmt = $pdo->prepare("SELECT profile_image FROM therapist_profile WHERE user_id = ?");
    $stmt->execute([$therapistId]);
    $oldProfile = $stmt->fetch();
    $oldImagePath = $oldProfile['profile_image'] ?? null;
    
    // Generate unique filename
    $newFileName = 'therapist_' . $therapistId . '_' . time() . '.' . $fileExtension;
    $uploadDir = '../../uploads/therapist_profiles/';
    $uploadPath = $uploadDir . $newFileName;
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Save path in database (relative path from root)
    $dbPath = 'uploads/therapist_profiles/' . $newFileName;
    
    $pdo->beginTransaction();
    
    // Update or insert profile
    $stmt = $pdo->prepare("SELECT id FROM therapist_profile WHERE user_id = ?");
    $stmt->execute([$therapistId]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE therapist_profile SET profile_image = ? WHERE user_id = ?");
        $stmt->execute([$dbPath, $therapistId]);
    } else {
        // If profile doesn't exist, create it (shouldn't happen, but just in case)
        $stmt = $pdo->prepare("INSERT INTO therapist_profile (user_id, profile_image) VALUES (?, ?)");
        $stmt->execute([$therapistId, $dbPath]);
    }
    
    $pdo->commit();
    
    // Delete old image if it exists and is different
    if ($oldImagePath && $oldImagePath !== $dbPath && file_exists('../../' . $oldImagePath)) {
        @unlink('../../' . $oldImagePath);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Profile picture uploaded successfully',
        'image_path' => $dbPath
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Delete uploaded file if database update failed
    if (isset($uploadPath) && file_exists($uploadPath)) {
        @unlink($uploadPath);
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
