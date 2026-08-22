<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$priority = trim($_POST['priority'] ?? 'medium');
$status = trim($_POST['status'] ?? 'active');

if (empty($id) || empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'ID, title and content are required']);
    exit();
}

// Validate priority
if (!in_array($priority, ['low', 'medium', 'high'])) {
    $priority = 'medium';
}

// Validate status
if (!in_array($status, ['active', 'inactive'])) {
    $status = 'active';
}

try {
    $stmt = $pdo->prepare("UPDATE notice_board SET title = ?, content = ?, priority = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $content, $priority, $status, $id]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Notice updated successfully'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
