<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'Kid ID is required'
        ]);
        exit;
    }
    
    // Delete kid from database
    $stmt = $pdo->prepare("DELETE FROM kids WHERE kid_id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Kid deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete kid'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
