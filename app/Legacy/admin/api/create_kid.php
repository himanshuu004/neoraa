<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? null;
    $parent_name = $_POST['parent_name'] ?? '';
    $case_type = $_POST['case_type'] ?? '';
    $contact = $_POST['contact'] ?? '';
    
    // Insert kid (no image handling for faster performance)
    $stmt = $pdo->prepare("INSERT INTO kids (kid_name, age, parent_name, case_type, contact) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $age, $parent_name, $case_type, $contact]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Kid added successfully',
        'kid_id' => $pdo->lastInsertId()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
