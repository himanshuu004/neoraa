<?php
require_once '../../config/config.php';
require_once '../../config/trainee_schema.php'; // Ensure tables exist
requireAdmin();

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT u.id as user_id, u.username, u.created_at, 
                         tp.name, tp.contact, tp.email
                         FROM users u
                         INNER JOIN roles r ON u.role_id = r.id
                         LEFT JOIN trainee_profile tp ON u.id = tp.user_id
                         WHERE r.role_name = 'trainee'
                         ORDER BY u.id DESC");
    $trainees = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $trainees
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>
