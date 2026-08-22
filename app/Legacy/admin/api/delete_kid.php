<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'Kid ID is required'
        ]);
        exit;
    }
    
    // Delete kid from database
    $stmt = $pdo->prepare("DELETE FROM kids WHERE kid_id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Kid deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete kid'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
