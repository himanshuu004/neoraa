<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$id     = (int)($_POST['id']     ?? 0);
$status = trim($_POST['status']  ?? '');

$allowed = ['new', 'reached_out', 'talked', 'closed'];
if (!$id || !in_array($status, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

$stmt = $pdo->prepare("UPDATE session_bookings SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

echo json_encode(['success' => true]);
