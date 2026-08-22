<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

// Set JSON header first to prevent redirects
header('Content-Type: application/json');

try {
    // Check authentication without redirecting (for AJAX)
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'therapist') {
        echo json_encode([
            'success' => false, 
            'message' => 'Authentication required. Please log in again.'
        ]);
        return;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    return;
}

try {
    ensure_notice_board_table();

    // Get only active notices
    $stmt = $pdo->prepare("SELECT nb.*, u.username as created_by_name 
                           FROM notice_board nb
                           LEFT JOIN users u ON nb.created_by = u.id
                           WHERE nb.status = 'active'
                           ORDER BY 
                               CASE nb.priority 
                                   WHEN 'high' THEN 1 
                                   WHEN 'medium' THEN 2 
                                   WHEN 'low' THEN 3 
                               END,
                               nb.created_at DESC");
    $stmt->execute();
    $notices = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true, 
        'data' => $notices
    ]);
} catch (PDOException $e) {
    // Handle database errors
    $errorMsg = $e->getMessage();
    if (strpos($errorMsg, "doesn't exist") !== false || 
        strpos($errorMsg, "Unknown table") !== false ||
        strpos($errorMsg, "Table") !== false && strpos($errorMsg, "doesn't exist") !== false) {
        // Table doesn't exist - return empty array
        echo json_encode([
            'success' => true, 
            'data' => []
        ]);
    } else {
        // Other database error
        echo json_encode([
            'success' => false, 
            'message' => 'Database error. Please contact administrator.',
            'data' => []
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error loading notices: ' . $e->getMessage(),
        'data' => []
    ]);
}
?>
