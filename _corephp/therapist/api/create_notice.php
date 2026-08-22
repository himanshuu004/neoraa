<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$priority = trim($_POST['priority'] ?? 'medium');
$status = trim($_POST['status'] ?? 'active');
$createdBy = $_SESSION['user_id'];

if (empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Title and content are required']);
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
    $stmt = $pdo->prepare("INSERT INTO notice_board (title, content, created_by, priority, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $createdBy, $priority, $status]);
    
    $noticeId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Notice created successfully',
        'notice_id' => $noticeId
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
