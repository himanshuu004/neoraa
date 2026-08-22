<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

try {
    $therapist_id = $_GET['therapist_id'] ?? null;
    
    if (!$therapist_id) {
        echo json_encode(['success' => false, 'message' => 'Therapist ID required']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM sessions WHERE therapist_id = ? ORDER BY 
                          FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), 
                          time_slot");
    $stmt->execute([$therapist_id]);
    $sessions = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $sessions]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
