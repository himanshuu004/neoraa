<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

// Ensure tables exist
header('Content-Type: application/json');

try {
    // Increase GROUP_CONCAT max length to handle many images
    $pdo->exec("SET SESSION group_concat_max_len = 10000");
    
    // Get all attendance records
    $stmt = $pdo->query("SELECT ta.*, tp.name as trainee_name, u.username as trainee_username
                         FROM trainee_attendance ta
                         LEFT JOIN trainee_profile tp ON ta.trainee_id = tp.user_id
                         LEFT JOIN users u ON ta.trainee_id = u.id
                         ORDER BY ta.session_date DESC, ta.created_at DESC");
    $records = $stmt->fetchAll();
    
    // Get all images for each attendance record separately to avoid GROUP_CONCAT limitations
    foreach ($records as &$record) {
        $imagePaths = [];
        
        // Fetch all images for this attendance record from trainee_session_images table
        $imgStmt = $pdo->prepare("SELECT image_path FROM trainee_session_images 
                                  WHERE attendance_id = ? 
                                  ORDER BY id ASC");
        $imgStmt->execute([$record['id']]);
        $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Filter out empty values and add to array
        foreach ($images as $path) {
            if (!empty(trim($path))) {
                $imagePaths[] = trim($path);
            }
        }
        
        // Fallback: if no images from separate table, check session_image column (for backward compatibility)
        if (empty($imagePaths) && !empty($record['session_image'])) {
            $imagePaths = explode(',', $record['session_image']);
            // Filter out empty values
            $imagePaths = array_filter($imagePaths, function($path) {
                return !empty(trim($path));
            });
            $imagePaths = array_values($imagePaths); // Re-index array
        }
        
        $record['image_paths'] = $imagePaths;
        $record['image_count'] = count($imagePaths);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $records
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>
