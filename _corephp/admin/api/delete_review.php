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
if ($id < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid review id']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT photo_path FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['photo_path'])) {
        $path = __DIR__ . '/../../' . $row['photo_path'];
        if (file_exists($path)) {
            @unlink($path);
        }
    }
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Review deleted']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
