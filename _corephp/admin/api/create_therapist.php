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
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($username) || empty($password) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Username, password, and name are required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get therapist role ID
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = 'therapist'");
    $stmt->execute();
    $role = $stmt->fetch();
    
    if (!$role) {
        throw new Exception('Therapist role not found');
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
    
    // Create therapist profile
    $stmt = $pdo->prepare("INSERT INTO therapist_profile (user_id, name, contact, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $contact ?: null, $email ?: null]);
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Therapist created successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
