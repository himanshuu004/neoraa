<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$id = (int)($_POST['id'] ?? 0);

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Notice ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM notice_board WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Notice deleted successfully'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
