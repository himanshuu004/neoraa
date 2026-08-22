<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT u.id, u.username, u.created_at
                         FROM users u
                         INNER JOIN roles r ON u.role_id = r.id
                         WHERE r.role_name = 'coordinator'
                         ORDER BY u.id DESC");
    $coordinators = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $coordinators
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>
