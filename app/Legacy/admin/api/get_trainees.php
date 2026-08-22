<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

// Ensure tables exist
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT u.id as user_id, u.username, u.created_at, 
                         tp.name, tp.contact, tp.email
                         FROM users u
                         INNER JOIN roles r ON u.role_id = r.id
                         LEFT JOIN trainee_profile tp ON u.id = tp.user_id
                         WHERE r.role_name = 'trainee'
                         ORDER BY u.id DESC");
    $trainees = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $trainees
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>
