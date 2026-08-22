<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $id = $_GET['id'] ?? 0;
    
    $stmt = $pdo->prepare("SELECT * FROM kids WHERE kid_id = ?");
    $stmt->execute([$id]);
    $kid = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($kid) {
        echo json_encode([
            'success' => true,
            'data' => $kid
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Kid not found'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
