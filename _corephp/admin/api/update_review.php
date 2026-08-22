<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/reviews_schema.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$text = trim($_POST['text'] ?? '');
$author = trim($_POST['author'] ?? '');
$location = trim($_POST['location'] ?? '');
$display_order = isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0;

if ($id < 1 || $text === '' || $author === '') {
    echo json_encode(['success' => false, 'message' => 'Valid id, review text and author are required']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE reviews SET `text` = ?, author = ?, `location` = ?, display_order = ? WHERE id = ?");
    $stmt->execute([$text, $author, $location ?: null, $display_order, $id]);
    echo json_encode(['success' => true, 'message' => 'Review updated']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
