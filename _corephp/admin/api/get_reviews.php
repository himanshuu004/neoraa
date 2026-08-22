<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/reviews_schema.php';
requireAdmin();

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, `text`, author, `location`, photo_path, display_order, created_at FROM reviews ORDER BY display_order ASC, id ASC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $reviews]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
}
