<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'trainee') {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    return;
}

/**
 * Compress and optimize image
 * @param string $sourcePath Path to source image
 * @param string $destinationPath Path to save compressed image
 * @param int $quality JPEG quality (1-100, lower = more compression)
 * @return bool Success status
 */
function compressImage($sourcePath, $destinationPath, $quality = 75) {
    // Check if GD library is available
    if (!function_exists('imagecreatefromjpeg') || !function_exists('imagejpeg')) {
        error_log("GD library not available for image compression");
        return false;
    }
    
    // Check if source file exists
    if (!file_exists($sourcePath)) {
        error_log("Source image file not found: " . $sourcePath);
        return false;
    }
    
    $imageInfo = @getimagesize($sourcePath);
    if (!$imageInfo) {
        error_log("Invalid image file or unable to read: " . $sourcePath);
        return false;
    }
    
    $mimeType = $imageInfo['mime'];
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    
    // Maximum dimensions for compression (maintain aspect ratio)
    $maxWidth = 1920;
    $maxHeight = 1920;
    
    // Calculate new dimensions if image is too large
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }
    
    // Create image resource based on type
    $sourceImage = false;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = @imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = @imagecreatefrompng($sourcePath);
            break;
        default:
            error_log("Unsupported image type: " . $mimeType);
            return false;
    }
    
    if (!$sourceImage) {
        error_log("Failed to create image resource from: " . $sourcePath);
        return false;
    }
    
    // Create new image with calculated dimensions
    $newImage = @imagecreatetruecolor($newWidth, $newHeight);
    if (!$newImage) {
        error_log("Failed to create new image resource");
        imagedestroy($sourceImage);
        return false;
    }
    
    // Preserve transparency for PNG (convert to white background for JPEG)
    if ($mimeType === 'image/png') {
        // Create white background for JPEG conversion
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);
    }
    
    // Resize image with better quality
    if (!@imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
        error_log("Failed to resize image");
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        return false;
    }
    
    // Ensure destination directory exists
    $destDir = dirname($destinationPath);
    if (!is_dir($destDir)) {
        if (!@mkdir($destDir, 0755, true)) {
            error_log("Failed to create destination directory: " . $destDir);
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            return false;
        }
    }
    
    // Always save as JPEG for better compression (convert PNG to JPEG)
    $result = @imagejpeg($newImage, $destinationPath, $quality);
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    // Verify file was created
    if ($result && file_exists($destinationPath) && filesize($destinationPath) > 0) {
        return true;
    } else {
        error_log("Image compression failed - file not created or empty: " . $destinationPath);
        return false;
    }
}

$trainee_id = $_SESSION['user_id'];
$session_date = $_POST['session_date'] ?? '';
$child_name = trim($_POST['child_name'] ?? '');
$activity_description = trim($_POST['activity_description'] ?? '');

if (empty($session_date) || empty($child_name) || empty($activity_description)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    return;
}

try {
    /**
     * Multiple Image Upload Flow:
     * 1. Multiple images are uploaded via $_FILES['session_images']
     * 2. Each image is validated, compressed, and saved to uploads/trainee_sessions/ folder
     * 3. Image paths are stored in trainee_session_images table linked to attendance record
     * 4. Comma-separated paths are also saved in session_image column of trainee_attendance table
     */
    
    // Insert attendance record first (without session_image, will update later)
    $stmt = $pdo->prepare("INSERT INTO trainee_attendance 
                          (trainee_id, session_date, child_name, activity_description) 
                          VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $trainee_id,
        $session_date,
        $child_name,
        $activity_description
    ]);
    
    $attendance_id = $pdo->lastInsertId();
    $savedImages = [];
    $uploadErrors = [];
    
    // Handle multiple image uploads
    // Check for both array format (session_images[]) and single file format
    $files = null;
    if (isset($_FILES['session_images']) && is_array($_FILES['session_images']['name'])) {
        $files = $_FILES['session_images'];
    } elseif (isset($_FILES['session_images'])) {
        // Single file upload - convert to array format
        $files = [
            'name' => [$_FILES['session_images']['name']],
            'type' => [$_FILES['session_images']['type']],
            'tmp_name' => [$_FILES['session_images']['tmp_name']],
            'error' => [$_FILES['session_images']['error']],
            'size' => [$_FILES['session_images']['size']]
        ];
    }
    
    if ($files && !empty($files['name'][0])) {
        // Use absolute path for better reliability
        $uploadDir = public_path('uploads/trainee_sessions').'/';
        
        // Normalize path separators
        $uploadDir = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $uploadDir);
        
        // Create directory structure if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!@mkdir($uploadDir, 0777, true)) {
                $uploadErrors[] = "Failed to create upload directory. Please contact administrator.";
                error_log("Failed to create upload directory: " . $uploadDir . " (Absolute path: " . realpath($uploadDir) . ")");
            } else {
                // Set permissions after creation
                @chmod($uploadDir, 0777);
            }
        }
        
        // Ensure directory exists and is writable
        if (!is_dir($uploadDir)) {
            $uploadErrors[] = "Upload directory does not exist and could not be created.";
            error_log("Upload directory does not exist: " . $uploadDir . " (Absolute path check: " . (file_exists($uploadDir) ? 'exists' : 'not found') . ")");
        } elseif (!is_writable($uploadDir)) {
            // Try to fix permissions
            @chmod($uploadDir, 0777);
            if (!is_writable($uploadDir)) {
                $uploadErrors[] = "Upload directory is not writable. Please check permissions.";
                $perms = file_exists($uploadDir) ? substr(sprintf('%o', fileperms($uploadDir)), -4) : 'unknown';
                error_log("Upload directory is not writable: " . $uploadDir . " (Current permissions: " . $perms . ", Absolute: " . realpath($uploadDir) . ")");
            }
        }
        
        // If directory issues, skip file processing
        $hasDirectoryError = false;
        foreach ($uploadErrors as $error) {
            if (stripos($error, 'directory') !== false) {
                $hasDirectoryError = true;
                break;
            }
        }
        
        if (!$hasDirectoryError) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/pjpeg'];
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $fileCount = count($files['name']);
        error_log("Processing " . $fileCount . " image file(s)");
        
        for ($i = 0; $i < $fileCount; $i++) {
            $uploadError = $files['error'][$i];
            
            if ($uploadError === UPLOAD_ERR_OK) {
                $fileType = $files['type'][$i];
                $fileSize = $files['size'][$i];
                $tmpName = $files['tmp_name'][$i];
                $originalName = $files['name'][$i];
                
                error_log("Processing image " . ($i + 1) . ": " . $originalName . " (Size: " . round($fileSize/1024, 2) . "KB, Type: " . $fileType . ")");
                
                // Check if temp file exists
                if (!file_exists($tmpName)) {
                    $uploadErrors[] = "Temporary file not found: " . $originalName;
                    error_log("Temporary file missing: " . $tmpName);
                    continue;
                }
                
                // Check file type
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                if (!in_array($fileType, $allowedTypes) && !in_array($extension, $allowedExtensions)) {
                    $uploadErrors[] = "Invalid file type: " . $originalName . " (Type: " . $fileType . ")";
                    error_log("Invalid file type: " . $fileType . " for file: " . $originalName);
                    continue;
                }
                
                // Check file size
                if ($fileSize > $maxSize) {
                    $uploadErrors[] = "File too large: " . $originalName . " (Max 5MB)";
                    error_log("File too large: " . round($fileSize/1024/1024, 2) . "MB for file: " . $originalName);
                    continue;
                }
                
                // Generate unique filename (always use .jpg for compressed images)
                $filename = 'session_' . $trainee_id . '_' . $attendance_id . '_' . time() . '_' . $i . '.jpg';
                $filepath = $uploadDir . $filename;
                $relativePath = 'uploads/trainee_sessions/' . $filename;
                
                // Try to compress and optimize image directly from temp file
                $compressionSuccess = false;
                if (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
                    $compressionSuccess = compressImage($tmpName, $filepath, 75);
                }
                
                if ($compressionSuccess && file_exists($filepath) && filesize($filepath) > 0) {
                    // Compression successful - save image path to trainee_session_images table
                    $imgStmt = $pdo->prepare("INSERT INTO trainee_session_images (attendance_id, image_path) VALUES (?, ?)");
                    $imgStmt->execute([$attendance_id, $relativePath]);
                    $savedImages[] = $relativePath;
                    
                    $originalSize = filesize($tmpName);
                    $compressedSize = filesize($filepath);
                    $compressionRatio = round((1 - ($compressedSize / $originalSize)) * 100, 1);
                    
                    error_log("Image compressed and saved: " . $relativePath . " (Original: " . round($originalSize/1024, 2) . "KB, Compressed: " . round($compressedSize/1024, 2) . "KB, Saved: " . $compressionRatio . "%)");
                } else {
                    // Compression failed or not available - fallback to direct file move
                    error_log("Compression failed or unavailable, using direct file move for: " . $originalName);
                    
                    // Determine final path based on original extension
                    $finalPath = $filepath;
                    $finalRelativePath = $relativePath;
                    
                    // If original was PNG and compression failed, keep PNG extension
                    if (strtolower($extension) === 'png') {
                        $finalPath = preg_replace('/\.jpg$/', '.png', $filepath);
                        $finalRelativePath = preg_replace('/\.jpg$/', '.png', $relativePath);
                    }
                    
                    // Ensure directory exists
                    $finalDir = dirname($finalPath);
                    if (!is_dir($finalDir)) {
                        if (!@mkdir($finalDir, 0755, true)) {
                            $uploadErrors[] = "Failed to create directory: " . $finalDir;
                            error_log("Failed to create directory: " . $finalDir);
                            continue;
                        }
                    }
                    
                    // Check if directory is writable
                    if (!is_writable($finalDir)) {
                        $uploadErrors[] = "Directory not writable: " . $finalDir;
                        error_log("Directory not writable: " . $finalDir);
                        continue;
                    }
                    
                    // Move uploaded file
                    if (@move_uploaded_file($tmpName, $finalPath)) {
                        // Verify file was saved
                        if (file_exists($finalPath) && filesize($finalPath) > 0) {
                            // Save image path to trainee_session_images table
                            $imgStmt = $pdo->prepare("INSERT INTO trainee_session_images (attendance_id, image_path) VALUES (?, ?)");
                            $imgStmt->execute([$attendance_id, $finalRelativePath]);
                            $savedImages[] = $finalRelativePath;
                            error_log("Image saved (without compression): " . $finalRelativePath . " (Size: " . round(filesize($finalPath)/1024, 2) . "KB)");
                        } else {
                            $uploadErrors[] = "File saved but verification failed: " . $originalName;
                            error_log("File verification failed: " . $finalPath);
                            // Clean up if file is invalid
                            if (file_exists($finalPath)) {
                                @unlink($finalPath);
                            }
                        }
                    } else {
                        $uploadErrors[] = "Failed to save: " . $originalName;
                        $lastError = error_get_last();
                        error_log("Failed to move uploaded file: " . $tmpName . " to " . $finalPath . " - Error: " . ($lastError ? $lastError['message'] : 'Unknown'));
                        
                        // Additional debugging
                        error_log("Temp file exists: " . (file_exists($tmpName) ? 'Yes' : 'No'));
                        error_log("Temp file readable: " . (is_readable($tmpName) ? 'Yes' : 'No'));
                        error_log("Destination directory exists: " . (is_dir($finalDir) ? 'Yes' : 'No'));
                        error_log("Destination directory writable: " . (is_writable($finalDir) ? 'Yes' : 'No'));
                    }
                }
            } else {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
                ];
                $errorMsg = $errorMessages[$uploadError] ?? 'Unknown upload error';
                $uploadErrors[] = "Upload error for: " . ($files['name'][$i] ?? 'unknown') . " - " . $errorMsg . " (Code: " . $uploadError . ")";
                error_log("Upload error for file " . ($i + 1) . ": " . $errorMsg . " (Code: " . $uploadError . ")");
            }
        }
        
            // Update session_image column in trainee_attendance table with comma-separated paths
            if (count($savedImages) > 0) {
                $sessionImagePaths = implode(',', $savedImages);
                $updateStmt = $pdo->prepare("UPDATE trainee_attendance SET session_image = ? WHERE id = ?");
                $updateStmt->execute([$sessionImagePaths, $attendance_id]);
            }
        }
    }
    
    $message = 'Attendance submitted successfully';
    if (count($savedImages) > 0) {
        $message .= ' with ' . count($savedImages) . ' image(s)';
    }
    if (count($uploadErrors) > 0) {
        $message .= '. Some images failed to upload: ' . implode(', ', $uploadErrors);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'images_saved' => count($savedImages),
        'image_paths' => $savedImages,
        'errors' => $uploadErrors,
        'record_id' => $attendance_id
    ]);
} catch (Exception $e) {
    error_log("Attendance submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
