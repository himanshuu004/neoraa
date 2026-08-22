<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    // Direct fetch - no unnecessary checks for speed
    $stmt = $pdo->query("
        SELECT 
            kid_id, 
            kid_name, 
            age, 
            parent_name, 
            contact, 
            case_type
        FROM kids 
        ORDER BY kid_id DESC
    ");
    $kids = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $kids,
        'count' => count($kids)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => [],
        'count' => 0
    ]);
}
