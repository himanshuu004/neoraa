<?php
require_once '../../config/config.php';
require_once '../../config/trainee_schema.php'; // Ensure tables exist
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($username) || empty($password) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Username, password, and name are required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get trainee role ID
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = 'trainee'");
    $stmt->execute();
    $role = $stmt->fetch();
    
    if (!$role) {
        throw new Exception('Trainee role not found');
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
    $stmt->execute([$username, $hashedPassword, $role['id']]);
    $userId = $pdo->lastInsertId();
    
    // Create trainee profile
    $stmt = $pdo->prepare("INSERT INTO trainee_profile (user_id, name, contact, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $contact ?: null, $email ?: null]);
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Trainee created successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
