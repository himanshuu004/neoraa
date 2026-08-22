<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $id = $_POST['kid_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? null;
    $parent_name = $_POST['parent_name'] ?? '';
    $case_type = $_POST['case_type'] ?? '';
    $contact = $_POST['contact'] ?? '';
    
    // Update kid (no image handling for faster performance)
    $stmt = $pdo->prepare("UPDATE kids 
                          SET kid_name = ?, age = ?, parent_name = ?, case_type = ?, contact = ?
                          WHERE kid_id = ?");
    $stmt->execute([$name, $age, $parent_name, $case_type, $contact, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Kid updated successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
