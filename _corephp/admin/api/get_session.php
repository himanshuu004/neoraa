<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM timetable WHERE id = ?");
$stmt->execute([$id]);
$session = $stmt->fetch();

if ($session) {
    echo json_encode(['success' => true, 'data' => $session]);
} else {
    echo json_encode(['success' => false, 'message' => 'Session not found']);
}
?>
