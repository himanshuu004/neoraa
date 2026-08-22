<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    // Check if notice_board table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'notice_board'");
    if ($checkTable->rowCount() === 0) {
        echo json_encode([
            'success' => true, 
            'data' => [],
            'message' => 'Notice board table does not exist. Please run the migration SQL.'
        ]);
        exit();
    }
    
    $stmt = $pdo->prepare("SELECT nb.*, u.username as created_by_name 
                           FROM notice_board nb
                           LEFT JOIN users u ON nb.created_by = u.id
                           ORDER BY nb.created_at DESC");
    $stmt->execute();
    $notices = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true, 
        'data' => $notices
    ]);
} catch (PDOException $e) {
    // Handle database errors
    if (strpos($e->getMessage(), "doesn't exist") !== false || strpos($e->getMessage(), "Unknown table") !== false) {
        echo json_encode([
            'success' => true, 
            'data' => [],
            'message' => 'Notice board table does not exist. Please run the migration SQL.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
