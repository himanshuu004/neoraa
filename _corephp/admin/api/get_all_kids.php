<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    // Direct fetch - no unnecessary checks for speed
    $stmt = $pdo->query("
        SELECT 
            kid_id, 
            kid_name, 
            age, 
            parent_name, 
            contact, 
            case_type
        FROM kids 
        ORDER BY kid_id DESC
    ");
    $kids = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $kids,
        'count' => count($kids)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => [],
        'count' => 0
    ]);
}
