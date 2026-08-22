<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    $id = $_GET['id'] ?? 0;
    
    $stmt = $pdo->prepare("SELECT * FROM kids WHERE kid_id = ?");
    $stmt->execute([$id]);
    $kid = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($kid) {
        echo json_encode([
            'success' => true,
            'data' => $kid
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Kid not found'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
