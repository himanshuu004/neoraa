<?php
require_once '../../config/config.php';
requireTherapist();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$therapistId = $_SESSION['user_id'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Update or insert therapist profile
    $stmt = $pdo->prepare("SELECT id FROM therapist_profile WHERE user_id = ?");
    $stmt->execute([$therapistId]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE therapist_profile SET name = ?, contact = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$name, $contact ?: null, $email ?: null, $therapistId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO therapist_profile (user_id, name, contact, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$therapistId, $name, $contact ?: null, $email ?: null]);
    }
    
    // Update password if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $therapistId]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Personal information updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
