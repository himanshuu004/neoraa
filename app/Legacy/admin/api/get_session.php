<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM timetable WHERE id = ?");
$stmt->execute([$id]);
$session = $stmt->fetch();

if ($session) {
    echo json_encode(['success' => true, 'data' => $session]);
} else {
    echo json_encode(['success' => false, 'message' => 'Session not found']);
}
?>
