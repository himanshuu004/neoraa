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

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get coordinator role ID
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = 'coordinator'");
    $stmt->execute();
    $role = $stmt->fetch();
    
    if (!$role) {
        // Create coordinator role if it doesn't exist
        $pdo->exec("INSERT INTO roles (role_name) VALUES ('coordinator')");
        $roleId = $pdo->lastInsertId();
    } else {
        $roleId = $role['id'];
    }
    
    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        throw new Exception('Username already exists');
    }
    
    // Create user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $roleId]);
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Coordinator created successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
