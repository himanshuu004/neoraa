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

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$text = trim($_POST['text'] ?? '');
$author = trim($_POST['author'] ?? '');
$location = trim($_POST['location'] ?? '');
$display_order = isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0;

if ($id < 1 || $text === '' || $author === '') {
    echo json_encode(['success' => false, 'message' => 'Valid id, review text and author are required']);
    return;
}

try {
    $stmt = $pdo->prepare("UPDATE reviews SET `text` = ?, author = ?, `location` = ?, display_order = ? WHERE id = ?");
    $stmt->execute([$text, $author, $location ?: null, $display_order, $id]);
    echo json_encode(['success' => true, 'message' => 'Review updated']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
