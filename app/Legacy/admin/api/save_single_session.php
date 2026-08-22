<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $admin_id = $_POST['admin_id'] ?? null;
    $day = $_POST['day'] ?? null;
    $time_slot_id = $_POST['time_slot_id'] ?? null;
    $time_slot_name = $_POST['time_slot_name'] ?? null;
    $kid_id = $_POST['kid_id'] ?? '';
    
    if (!$admin_id || !$day || !$time_slot_id) {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
        exit;
    }
    
    // Get time slot display name from database if not provided
    if (!$time_slot_name) {
        $timeStmt = $pdo->prepare("SELECT display_name FROM time_slots WHERE id = ?");
        $timeStmt->execute([$time_slot_id]);
        $timeSlot = $timeStmt->fetch();
        $time_slot_name = $timeSlot ? $timeSlot['display_name'] : null;
    }
    
    if (!$time_slot_name) {
        echo json_encode(['success' => false, 'message' => 'Invalid time slot']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if session already exists
    $checkStmt = $pdo->prepare("SELECT id FROM sessions WHERE therapist_id = ? AND day_of_week = ? AND time_slot = ?");
    $checkStmt->execute([$admin_id, $day, $time_slot_name]);
    $existing = $checkStmt->fetch();
    
    if ($kid_id && $kid_id != '') {
        // Kid selected - insert or update
        $kidStmt = $pdo->prepare("SELECT kid_name FROM kids WHERE kid_id = ?");
        $kidStmt->execute([$kid_id]);
        $kid = $kidStmt->fetch();
        
        if (!$kid) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Kid not found']);
            exit;
        }
        
        if ($existing) {
            // Update existing session
            $stmt = $pdo->prepare("UPDATE sessions 
                                  SET kid_id = ?, client_name = ?, session_type = ?, special_notes = ?, updated_at = CURRENT_TIMESTAMP 
                                  WHERE id = ?");
            $stmt->execute([$kid_id, $kid['kid_name'], 'Therapy Session', 'Kid ID: ' . $kid_id, $existing['id']]);
            $message = 'Updated: ' . $kid['kid_name'];
        } else {
            // Insert new session
            $stmt = $pdo->prepare("INSERT INTO sessions (therapist_id, day_of_week, time_slot, kid_id, client_name, session_type, special_notes) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$admin_id, $day, $time_slot_name, $kid_id, $kid['kid_name'], 'Therapy Session', 'Kid ID: ' . $kid_id]);
            $message = 'Saved: ' . $kid['kid_name'];
        }
    } else {
        // Kid cleared - delete session if exists
        if ($existing) {
            $stmt = $pdo->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute([$existing['id']]);
            $message = 'Cleared';
        } else {
            $message = 'Already empty';
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => [
            'admin_id' => $admin_id,
            'day' => $day,
            'time_slot' => $time_slot_name,
            'kid_id' => $kid_id
        ]
    ]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
