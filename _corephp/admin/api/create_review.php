<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/reviews_schema.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$text = trim($_POST['text'] ?? '');
$author = trim($_POST['author'] ?? '');
$location = trim($_POST['location'] ?? '');
$display_order = isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0;

if ($text === '' || $author === '') {
    echo json_encode(['success' => false, 'message' => 'Review text and author are required']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO reviews (`text`, author, `location`, display_order) VALUES (?, ?, ?, ?)");
    $stmt->execute([$text, $author, $location ?: null, $display_order]);
    $id = (int) $pdo->lastInsertId();
    echo json_encode(['success' => true, 'message' => 'Review created', 'id' => $id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
