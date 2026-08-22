<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT u.id, u.username, u.created_at
                         FROM users u
                         INNER JOIN roles r ON u.role_id = r.id
                         WHERE r.role_name = 'coordinator'
                         ORDER BY u.id DESC");
    $coordinators = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $coordinators
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>
