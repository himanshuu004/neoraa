<?php
header('Content-Type: application/json');

try {
    require_once '../../config/config.php';
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'trainee') {
        echo json_encode([
            'success' => false, 
            'message' => 'Authentication required.'
        ]);
        exit();
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    exit();
}

try {
    $checkTable = $pdo->query("SHOW TABLES LIKE 'notice_board'");
    if ($checkTable->rowCount() === 0) {
        echo json_encode([
            'success' => true, 
            'data' => []
        ]);
        exit();
    }
    
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
    echo json_encode([
        'success' => true, 
        'data' => []
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error loading notices: ' . $e->getMessage(),
        'data' => []
    ]);
}
?>
