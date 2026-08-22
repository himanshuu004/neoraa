<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    // Check if time_slots table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'time_slots'");
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Time slots table does not exist. Please run database_update.sql first.', 'data' => []]);
        return;
    }
    
    $stmt = $pdo->query("SELECT * FROM time_slots ORDER BY sort_order, time_start");
    $timeSlots = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $timeSlots]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
}
?>
