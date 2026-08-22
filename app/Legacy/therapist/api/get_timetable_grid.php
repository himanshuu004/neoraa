<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

$therapist_id = $_SESSION['user_id'];

try {
    // Get time slots - handle both old and new column names
    $checkStmt = $pdo->query("SHOW COLUMNS FROM time_slots LIKE 'start_time'");
    $hasNewColumns = ($checkStmt->rowCount() > 0);
    
    if ($hasNewColumns) {
        $timeSlotsStmt = $pdo->query("SELECT id, start_time, end_time, display_time, sort_order FROM time_slots ORDER BY sort_order, start_time");
        $rows = $timeSlotsStmt->fetchAll(PDO::FETCH_ASSOC);
        // Normalize to consistent format
        $timeSlots = [];
        foreach ($rows as $row) {
            $timeSlots[] = [
                'id' => $row['id'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'display_time' => $row['display_time'],
                'sort_order' => $row['sort_order']
            ];
        }
    } else {
        $timeSlotsStmt = $pdo->query("SELECT id, time_start, time_end, display_name, sort_order FROM time_slots ORDER BY sort_order, time_start");
        $rows = $timeSlotsStmt->fetchAll(PDO::FETCH_ASSOC);
        // Normalize to consistent format
        $timeSlots = [];
        foreach ($rows as $row) {
            $timeSlots[] = [
                'id' => $row['id'],
                'start_time' => $row['time_start'],
                'end_time' => $row['time_end'],
                'display_time' => $row['display_name'],
                'sort_order' => $row['sort_order']
            ];
        }
    }
    
    // Check if timetable_grid table exists
    $tableExists = false;
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'timetable_grid'");
        $tableExists = ($checkTable->rowCount() > 0);
    } catch (Exception $e) {
        $tableExists = false;
    }
    
    $timetableRows = [];
    if ($tableExists) {
        // Get timetable data from timetable_grid - handle both column name formats
        if ($hasNewColumns) {
            // New structure: start_time, end_time, display_time
            $timetableStmt = $pdo->prepare("SELECT tg.*, k.kid_name, ts.start_time, ts.display_time
                                           FROM timetable_grid tg
                                           LEFT JOIN kids k ON tg.kid_id = k.kid_id
                                           JOIN time_slots ts ON tg.time_slot_id = ts.id
                                           WHERE tg.therapist_id = ?
                                           ORDER BY tg.day_of_week, ts.sort_order");
        } else {
            // Old structure: time_start, time_end, display_name
            $timetableStmt = $pdo->prepare("SELECT tg.*, k.kid_name, ts.time_start as start_time, ts.display_name as display_time
                                           FROM timetable_grid tg
                                           LEFT JOIN kids k ON tg.kid_id = k.kid_id
                                           JOIN time_slots ts ON tg.time_slot_id = ts.id
                                           WHERE tg.therapist_id = ?
                                           ORDER BY tg.day_of_week, ts.sort_order");
        }
        
        $timetableStmt->execute([$therapist_id]);
        $timetableRows = $timetableStmt->fetchAll();
    }
    
    // Organize data by day and time_slot_id
    $timetableData = [];
    foreach ($timetableRows as $row) {
        $day = $row['day_of_week'];
        $timeSlotId = (int)$row['time_slot_id'];
        // Ensure kid_id is properly cast (int or null)
        $kidId = null;
        if (isset($row['kid_id']) && $row['kid_id'] !== null && $row['kid_id'] !== '' && $row['kid_id'] !== '0') {
            $kidId = (int)$row['kid_id'];
            if ($kidId <= 0) {
                $kidId = null;
            }
        }
        $timetableData[$day][$timeSlotId] = [
            'id' => (int)$row['id'],
            'kid_name' => $row['kid_name'] ?? null,
            'kid_id' => $kidId,
            'time_slot_id' => $timeSlotId
        ];
    }
    
    echo json_encode([
        'success' => true,
        'time_slots' => $timeSlots,
        'timetable' => $timetableData,
        'table_exists' => $tableExists
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'error_details' => $e->getFile() . ':' . $e->getLine()
    ]);
}
?>
