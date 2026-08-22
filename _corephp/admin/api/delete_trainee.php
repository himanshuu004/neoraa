<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid trainee ID']);
    exit();
}

try {
    // Check if user is trainee
    $stmt = $pdo->prepare("SELECT u.id FROM users u 
                          INNER JOIN roles r ON u.role_id = r.id 
                          WHERE u.id = ? AND r.role_name = 'trainee'");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception('Trainee not found');
    }
    
    // Delete will cascade to trainee_profile and trainee_attendance due to foreign keys
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Trainee deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
