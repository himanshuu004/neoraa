<?php
require_once '../../config/config.php';
requireAdmin();

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
