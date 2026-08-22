<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$therapist_id = $_POST['therapist_id'] ?? 0;
$day_of_week = $_POST['day_of_week'] ?? '';
$time_slot = $_POST['time_slot'] ?? '';
$client_name = trim($_POST['client_name'] ?? '');
$session_type = trim($_POST['session_type'] ?? '');
$special_notes = trim($_POST['special_notes'] ?? '');

if (empty($therapist_id) || empty($day_of_week) || empty($time_slot) || empty($client_name)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO timetable (therapist_id, day_of_week, time_slot, client_name, session_type, special_notes) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$therapist_id, $day_of_week, $time_slot, $client_name, $session_type ?: null, $special_notes ?: null]);
    
    echo json_encode(['success' => true, 'message' => 'Session created successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
