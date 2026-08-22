<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $therapist_id = $_POST['therapist_id'] ?? null;
    $grid_data = isset($_POST['grid_data']) ? json_decode($_POST['grid_data'], true) : null;
    
    if (!$therapist_id || !$grid_data) {
        echo json_encode(['success' => false, 'message' => 'Missing therapist ID or grid data']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete existing sessions for this therapist
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE therapist_id = ?");
    $stmt->execute([$therapist_id]);
    
    // Insert new timetable data (using time_slots from database only - NO CUSTOM TIMING)
    $stmt = $pdo->prepare("INSERT INTO sessions (therapist_id, day_of_week, time_slot, client_name, kid_id, session_type, special_notes) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $inserted = 0;
    foreach ($grid_data as $day => $timeSlots) {
        foreach ($timeSlots as $timeSlotId => $kidId) {
            if ($kidId && $kidId != '') {
                // Get kid name
                $kidStmt = $pdo->prepare("SELECT kid_name FROM kids WHERE kid_id = ?");
                $kidStmt->execute([$kidId]);
                $kid = $kidStmt->fetch();
                
                // Get time slot info from time_slots table (pre-configured slots only)
                $timeStmt = $pdo->prepare("SELECT display_name FROM time_slots WHERE id = ?");
                $timeStmt->execute([$timeSlotId]);
                $timeSlot = $timeStmt->fetch();
                
                if ($kid && $timeSlot) {
                    $stmt->execute([
                        $therapist_id,
                        $day,
                        $timeSlot['display_name'],  // This will be stored in time_slot column (after converting to VARCHAR)
                        $kid['kid_name'],
                        $kidId,  // Store kid_id directly
                        'Therapy Session',
                        'Kid ID: ' . $kidId
                    ]);
                    $inserted++;
                }
            }
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Timetable saved successfully!',
        'inserted' => $inserted
    ]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
