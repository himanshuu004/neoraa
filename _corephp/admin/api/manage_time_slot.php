<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $time_start = $_POST['time_start'] ?? '';
    $time_end = $_POST['time_end'] ?? '';
    $display_name = $_POST['display_name'] ?? '';
    $id = $_POST['id'] ?? 0;
    
    try {
        if ($action === 'add') {
            // Get max sort_order
            $stmt = $pdo->query("SELECT MAX(sort_order) as max_order FROM time_slots");
            $result = $stmt->fetch();
            $sort_order = ($result['max_order'] ?? 0) + 1;
            
            $stmt = $pdo->prepare("INSERT INTO time_slots (time_start, time_end, display_name, sort_order) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$time_start, $time_end, $display_name, $sort_order]);
            
            echo json_encode(['success' => true, 'message' => 'Time slot added', 'id' => $pdo->lastInsertId()]);
        } elseif ($action === 'delete' && $id > 0) {
            $stmt = $pdo->prepare("DELETE FROM time_slots WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Time slot deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
