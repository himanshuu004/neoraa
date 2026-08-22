<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    return;
}

$id = $_POST['id'] ?? 0;
$therapist_id = $_POST['therapist_id'] ?? 0;
$day_of_week = $_POST['day_of_week'] ?? '';
$time_slot = $_POST['time_slot'] ?? '';
$client_name = trim($_POST['client_name'] ?? '');
$session_type = trim($_POST['session_type'] ?? '');
$special_notes = trim($_POST['special_notes'] ?? '');

if (empty($therapist_id) || empty($day_of_week) || empty($time_slot) || empty($client_name)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    return;
}

try {
    $stmt = $pdo->prepare("UPDATE timetable SET therapist_id = ?, day_of_week = ?, time_slot = ?, 
                           client_name = ?, session_type = ?, special_notes = ? WHERE id = ?");
    $stmt->execute([$therapist_id, $day_of_week, $time_slot, $client_name, $session_type ?: null, $special_notes ?: null, $id]);
    
    echo json_encode(['success' => true, 'message' => 'Session updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
