<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    ensure_notice_board_table();

    $stmt = $pdo->prepare("SELECT nb.id, nb.title, nb.content, nb.created_by, nb.priority, nb.status, nb.created_at,
                                  u.username as created_by_name
                           FROM notice_board nb
                           LEFT JOIN users u ON nb.created_by = u.id
                           ORDER BY nb.created_at DESC");
    $stmt->execute();
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $notices,
        'count' => count($notices),
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
}
