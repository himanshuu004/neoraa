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

$trainee_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT ta.*, 
                           GROUP_CONCAT(tsi.image_path ORDER BY tsi.id SEPARATOR '|||') as images
                           FROM trainee_attendance ta
                           LEFT JOIN trainee_session_images tsi ON ta.id = tsi.attendance_id
                           WHERE ta.trainee_id = ? 
                           GROUP BY ta.id
                           ORDER BY ta.session_date DESC, ta.created_at DESC 
                           LIMIT 50");
    $stmt->execute([$trainee_id]);
    $records = $stmt->fetchAll();
    
    // Process images for each record
    foreach ($records as &$record) {
        if (!empty($record['images'])) {
            $record['image_paths'] = explode('|||', $record['images']);
        } else {
            $record['image_paths'] = [];
        }
        unset($record['images']); // Remove the concatenated string
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
