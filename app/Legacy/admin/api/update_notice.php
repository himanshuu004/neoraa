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

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$priority = trim($_POST['priority'] ?? 'medium');
$status = trim($_POST['status'] ?? 'active');

if ($id < 1 || $title === '' || $content === '') {
    echo json_encode(['success' => false, 'message' => 'ID, title and content are required']);
    return;
}

if (!in_array($priority, ['low', 'medium', 'high'], true)) {
    $priority = 'medium';
}

if (!in_array($status, ['active', 'inactive'], true)) {
    $status = 'active';
}

try {
    ensure_notice_board_table();

    $stmt = $pdo->prepare("UPDATE notice_board SET title = ?, content = ?, priority = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $content, $priority, $status, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Notice updated successfully',
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
