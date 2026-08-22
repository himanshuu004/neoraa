<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $therapist_id = $_POST['therapist_id'] ?? null;
    $day = $_POST['day'] ?? null;
    $time_slot = $_POST['time_slot'] ?? null;
    $kid_id = $_POST['kid_id'] ?? null;
    
    if (!$therapist_id || !$day || !$time_slot) {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
        return;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if session already exists
    $checkStmt = $pdo->prepare("SELECT id FROM sessions WHERE therapist_id = ? AND day_of_week = ? AND time_slot = ?");
    $checkStmt->execute([$therapist_id, $day, $time_slot]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($kid_id && $kid_id != '') {
        // Get kid name
        $kidStmt = $pdo->prepare("SELECT kid_name FROM kids WHERE kid_id = ?");
        $kidStmt->execute([$kid_id]);
        $kid = $kidStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$kid) {
            throw new Exception('Kid not found');
        }
        
        if ($existing) {
            // Update existing session
            $stmt = $pdo->prepare("UPDATE sessions 
                                  SET kid_id = ?, client_name = ?, session_type = ?, special_notes = ?, updated_at = CURRENT_TIMESTAMP
                                  WHERE id = ?");
            $stmt->execute([$kid_id, $kid['kid_name'], 'Therapy Session', 'Kid ID: ' . $kid_id, $existing['id']]);
        } else {
            // Insert new session
            $stmt = $pdo->prepare("INSERT INTO sessions (therapist_id, day_of_week, time_slot, kid_id, client_name, session_type, special_notes) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$therapist_id, $day, $time_slot, $kid_id, $kid['kid_name'], 'Therapy Session', 'Kid ID: ' . $kid_id]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Saved!',
            'kid_name' => $kid['kid_name']
        ]);
    } else {
        // Delete session if kid is cleared
        if ($existing) {
            $stmt = $pdo->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute([$existing['id']]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cleared!',
            'kid_name' => null
        ]);
    }
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
