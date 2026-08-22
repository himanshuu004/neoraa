<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    // Check if kids table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'kids'");
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Kids table does not exist. Please run database_update.sql first.', 'data' => []]);
        exit();
    }
    
    $stmt = $pdo->query("SELECT kid_id, kid_name, age, parent_name, contact FROM kids ORDER BY kid_name");
    $kids = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $kids]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
}
?>
