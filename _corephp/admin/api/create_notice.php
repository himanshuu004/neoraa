<?php
// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header first
header('Content-Type: application/json');

try {
    require_once '../../config/config.php';
    requireAdmin();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    exit();
}

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
    // Check if notice_board table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'notice_board'");
    if ($checkTable->rowCount() === 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Notice board table does not exist. Please run the migration SQL to create the notice_board table.'
        ]);
        exit();
    }
    
    $stmt = $pdo->prepare("INSERT INTO notice_board (title, content, created_by, priority, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $createdBy, $priority, $status]);
    
    $noticeId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Notice created successfully',
        'notice_id' => $noticeId
    ]);
} catch (PDOException $e) {
    // Handle database errors
    if (strpos($e->getMessage(), "doesn't exist") !== false || strpos($e->getMessage(), "Unknown table") !== false) {
        echo json_encode([
            'success' => false, 
            'message' => 'Notice board table does not exist. Please run the migration SQL to create the notice_board table.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
