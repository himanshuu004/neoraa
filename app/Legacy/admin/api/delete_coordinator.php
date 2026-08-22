<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    return;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid coordinator ID']);
    return;
}

try {
    // Check if user is coordinator
    $stmt = $pdo->prepare("SELECT u.id FROM users u 
                          INNER JOIN roles r ON u.role_id = r.id 
                          WHERE u.id = ? AND r.role_name = 'coordinator'");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception('Coordinator not found');
    }
    
    // Don't allow deleting yourself
    if ($id == $_SESSION['user_id']) {
        throw new Exception('You cannot delete your own account');
    }
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Coordinator deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
