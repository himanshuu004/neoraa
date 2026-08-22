<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    // Check if kids table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'kids'");
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Kids table does not exist. Please run database_update.sql first.', 'data' => []]);
        return;
    }
    
    $stmt = $pdo->query("SELECT kid_id, kid_name, age, parent_name, contact FROM kids ORDER BY kid_name");
    $kids = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $kids]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
}
?>
