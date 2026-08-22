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

$id = $_POST['id'] ?? 0;
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($username) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Username and name are required']);
    return;
}

try {
    $pdo->beginTransaction();
    
    // Check if user exists and is therapist
    $stmt = $pdo->prepare("SELECT u.id FROM users u 
                           JOIN roles r ON u.role_id = r.id 
                           WHERE u.id = ? AND r.role_name = 'therapist'");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception('Therapist not found');
    }
    
    // Check if username is taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) {
        throw new Exception('Username already taken');
    }
    
    // Update username
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$username, $id]);
    
    // Update password if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $id]);
    }
    
    // Update or insert profile
    $stmt = $pdo->prepare("SELECT id FROM therapist_profile WHERE user_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE therapist_profile SET name = ?, contact = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$name, $contact ?: null, $email ?: null, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO therapist_profile (user_id, name, contact, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $name, $contact ?: null, $email ?: null]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Therapist updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
