<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    
    $stmt = $pdo->prepare("DELETE FROM timetable WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Timetable entry deleted successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
