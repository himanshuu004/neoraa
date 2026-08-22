<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

$therapist_id = $_GET['therapist_id'] ?? 0;

if (empty($therapist_id)) {
    echo json_encode(['success' => false, 'message' => 'Therapist ID required']);
    exit();
}

try {
    // NO CUSTOM TIMING - Get data from sessions table directly
    $stmt = $pdo->prepare("SELECT day_of_week, time_slot, special_notes
                          FROM sessions
                          WHERE therapist_id = ?
                          ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), 
                                   time_slot");
    $stmt->execute([$therapist_id]);
    $existingSessions = $stmt->fetchAll();
    
    // Organize by day and time slot display name
    $grid = [];
    foreach ($existingSessions as $session) {
        $day = $session['day_of_week'];
        $time_slot = $session['time_slot'];
        
        // Extract kid ID from special notes
        $kid_id = null;
        if (!empty($session['special_notes'])) {
            preg_match('/Kid ID: (\d+)/', $session['special_notes'], $matches);
            $kid_id = isset($matches[1]) ? $matches[1] : null;
        }
        
        if ($day && $time_slot) {
            if (!isset($grid[$day])) {
                $grid[$day] = [];
            }
            $grid[$day][$time_slot] = $kid_id;
        }
    }
    
    echo json_encode([
        'success' => true, 
        'data' => $grid
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
