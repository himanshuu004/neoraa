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

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($username) || empty($password) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Username, password, and name are required']);
    return;
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
