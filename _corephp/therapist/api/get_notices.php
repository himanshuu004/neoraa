<?php
// Set JSON header first to prevent redirects
header('Content-Type: application/json');

try {
    require_once '../../config/config.php';
    
    // Check authentication without redirecting (for AJAX)
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'therapist') {
        echo json_encode([
            'success' => false, 
            'message' => 'Authentication required. Please log in again.'
        ]);
        exit();
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    exit();
}

try {
    // Check if notice_board table exists
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'notice_board'");
        if ($checkTable->rowCount() === 0) {
            echo json_encode([
                'success' => true, 
                'data' => []
            ]);
            exit();
        }
    } catch (PDOException $e) {
        // Table check failed - table probably doesn't exist
        echo json_encode([
            'success' => true, 
            'data' => []
        ]);
        exit();
    }
    
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
