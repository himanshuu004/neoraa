<?php
require_once '../../config/config.php';
requireAdmin();

header('Content-Type: application/json');

// Log everything
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'admin_id' => $_SESSION['user_id'],
    'post_data' => $_POST,
    'therapist_id' => $_POST['therapist_id'] ?? null,
    'grid_data_raw' => $_POST['grid_data'] ?? null,
];

try {
    $therapist_id = $_POST['therapist_id'] ?? null;
    $grid_data = isset($_POST['grid_data']) ? json_decode($_POST['grid_data'], true) : null;
    
    $logData['therapist_id_parsed'] = $therapist_id;
    $logData['grid_data_parsed'] = $grid_data;
    
    if (!$therapist_id || !$grid_data) {
        $logData['error'] = 'Missing therapist ID or grid data';
        echo json_encode(['success' => false, 'message' => 'Missing data', 'debug' => $logData]);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Check existing sessions BEFORE delete
    $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM sessions WHERE therapist_id = ?");
    $checkStmt->execute([$therapist_id]);
    $beforeCount = $checkStmt->fetch()['count'];
    $logData['sessions_before_delete'] = $beforeCount;
    
    // Delete existing sessions for this therapist
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE therapist_id = ?");
    $stmt->execute([$therapist_id]);
    $logData['deleted_count'] = $stmt->rowCount();
    
    // Prepare insert statement
    $stmt = $pdo->prepare("INSERT INTO sessions (therapist_id, day_of_week, time_slot, client_name, kid_id, session_type, special_notes) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $inserted = 0;
    $logData['insert_details'] = [];
    
    foreach ($grid_data as $day => $timeSlots) {
        foreach ($timeSlots as $timeSlotId => $kidId) {
            if ($kidId && $kidId != '') {
                // Get kid name
                $kidStmt = $pdo->prepare("SELECT kid_name FROM kids WHERE kid_id = ?");
                $kidStmt->execute([$kidId]);
                $kid = $kidStmt->fetch();
                
                // Get time slot info from time_slots table
                $timeStmt = $pdo->prepare("SELECT display_name FROM time_slots WHERE id = ?");
                $timeStmt->execute([$timeSlotId]);
                $timeSlot = $timeStmt->fetch();
                
                $insertDetail = [
                    'day' => $day,
                    'timeSlotId' => $timeSlotId,
                    'kidId' => $kidId,
                    'kid' => $kid,
                    'timeSlot' => $timeSlot
                ];
                
                if ($kid && $timeSlot) {
                    $stmt->execute([
                        $therapist_id,
                        $day,
                        $timeSlot['display_name'],
                        $kid['kid_name'],
                        $kidId,
                        'Therapy Session',
                        'Kid ID: ' . $kidId
                    ]);
                    $inserted++;
                    $insertDetail['inserted'] = true;
                } else {
                    $insertDetail['inserted'] = false;
                    $insertDetail['reason'] = 'Kid or TimeSlot not found';
                }
                
                $logData['insert_details'][] = $insertDetail;
            }
        }
    }
    
    $logData['total_inserted'] = $inserted;
    
    // Check sessions AFTER insert
    $checkStmt->execute([$therapist_id]);
    $afterCount = $checkStmt->fetch()['count'];
    $logData['sessions_after_insert'] = $afterCount;
    
    // Get all sessions for this therapist
    $verifyStmt = $pdo->prepare("SELECT * FROM sessions WHERE therapist_id = ? ORDER BY day_of_week, time_slot");
    $verifyStmt->execute([$therapist_id]);
    $logData['final_sessions'] = $verifyStmt->fetchAll();
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Timetable saved successfully!',
        'inserted' => $inserted,
        'debug' => $logData
    ]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $logData['exception'] = $e->getMessage();
    $logData['trace'] = $e->getTraceAsString();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'debug' => $logData]);
}
?>
