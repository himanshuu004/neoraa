<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$adminId = $_SESSION['user_id'];

if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Username is required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Check if username is taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $adminId]);
    if ($stmt->fetch()) {
        throw new Exception('Username already taken');
    }
    
    // Update username
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$username, $adminId]);
    
    // Update password if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $adminId]);
    }
    
    // Update session username
    $_SESSION['username'] = $username;
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Personal information updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
