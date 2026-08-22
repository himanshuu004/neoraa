<?php
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/trainee_schema.php'; // Ensure tables exist

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'trainee') {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$trainee_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit();
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
