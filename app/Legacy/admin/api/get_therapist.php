<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT u.id, u.username, tp.name, tp.contact, tp.email
                       FROM users u
                       LEFT JOIN therapist_profile tp ON u.id = tp.user_id
                       WHERE u.id = ?");
$stmt->execute([$id]);
$therapist = $stmt->fetch();

if ($therapist) {
    echo json_encode(['success' => true, 'data' => $therapist]);
} else {
    echo json_encode(['success' => false, 'message' => 'Therapist not found']);
}
?>
