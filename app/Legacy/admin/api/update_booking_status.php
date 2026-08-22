<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$id     = (int)($_POST['id']     ?? 0);
$status = trim($_POST['status']  ?? '');

$allowed = ['new', 'reached_out', 'talked', 'closed'];
if (!$id || !in_array($status, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

$stmt = $pdo->prepare("UPDATE session_bookings SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

echo json_encode(['success' => true]);
