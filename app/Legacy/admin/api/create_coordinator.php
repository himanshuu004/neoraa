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

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    return;
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
