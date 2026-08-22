<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT u.id, u.username, tp.name, tp.contact, tp.email
                       FROM users u
                       LEFT JOIN therapist_profile tp ON u.id = tp.user_id
                       WHERE u.id = ?");
$stmt->execute([$id]);
$therapist = $stmt->fetch();

if ($therapist) {
    echo json_encode(['success' => true, 'data' => $therapist]);
} else {
    echo json_encode(['success' => false, 'message' => 'Therapist not found']);
}
?>
