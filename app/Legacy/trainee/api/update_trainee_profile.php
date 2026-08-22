<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

// Ensure tables exist

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'trainee') {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    return;
}

$trainee_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    return;
}

try {
    $pdo->beginTransaction();
    
    // Update trainee profile
    $stmt = $pdo->prepare("INSERT INTO trainee_profile (user_id, name, contact, email) 
                          VALUES (?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE 
                          name = VALUES(name),
                          contact = VALUES(contact),
                          email = VALUES(email)");
    $stmt->execute([$trainee_id, $name, $contact ?: null, $email ?: null]);
    
    // Update password if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $trainee_id]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
