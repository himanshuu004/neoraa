<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    // Check if time_slots table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'time_slots'");
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Time slots table does not exist. Please run database_update.sql first.', 'data' => []]);
        exit();
    }
    
    $stmt = $pdo->query("SELECT * FROM time_slots ORDER BY sort_order, time_start");
    $timeSlots = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $timeSlots]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
}
?>
