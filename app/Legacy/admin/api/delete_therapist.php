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

try {
    // Check if user exists and is therapist
    $stmt = $pdo->prepare("SELECT u.id FROM users u 
                           JOIN roles r ON u.role_id = r.id 
                           WHERE u.id = ? AND r.role_name = 'therapist'");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception('Therapist not found');
    }
    
    // Delete user (cascade will delete profile, timetable, sessions)
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Therapist deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
