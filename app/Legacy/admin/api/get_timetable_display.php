<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

$therapist_id = $_GET['therapist_id'] ?? 0;

if (empty($therapist_id)) {
    echo json_encode(['success' => false, 'message' => 'Therapist ID required']);
    return;
}

try {
    // Get therapist name
    $therapistStmt = $pdo->prepare("SELECT u.username, tp.name 
                                    FROM users u 
                                    LEFT JOIN therapist_profile tp ON u.id = tp.user_id 
                                    WHERE u.id = ?");
    $therapistStmt->execute([$therapist_id]);
    $therapist = $therapistStmt->fetch();
    $therapistName = $therapist ? ($therapist['name'] ?: $therapist['username']) : 'Unknown';
    
    // Get kids list
    $kidsStmt = $pdo->query("SELECT kid_id, kid_name FROM kids ORDER BY kid_name");
    $kids = $kidsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get time slots from database
    $timeSlotsStmt = $pdo->query("SELECT id, display_name, time_start, time_end, sort_order 
                                  FROM time_slots 
                                  ORDER BY sort_order, time_start");
    $timeSlots = $timeSlotsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($timeSlots)) {
        echo json_encode([
            'success' => false, 
            'message' => 'No time slots found in database'
        ]);
        return;
    }
    
    // Get sessions for this therapist
    $sessionsStmt = $pdo->prepare("SELECT day_of_week, time_slot, client_name, kid_id
                                   FROM sessions
                                   WHERE therapist_id = ?
                                   ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
                                            time_slot");
    $sessionsStmt->execute([$therapist_id]);
    $sessions = $sessionsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize sessions by day and time slot
    $grid = [];
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    
    foreach ($days as $day) {
        $grid[$day] = [];
        foreach ($timeSlots as $slot) {
            $grid[$day][$slot['display_name']] = [
                'client_name' => '',
                'kid_id' => null
            ];
        }
    }
    
    // Fill in actual session data
    foreach ($sessions as $session) {
        $day = $session['day_of_week'];
        $timeSlot = $session['time_slot'];
        
        if (isset($grid[$day][$timeSlot])) {
            $grid[$day][$timeSlot]['client_name'] = $session['client_name'];
            $grid[$day][$timeSlot]['kid_id'] = $session['kid_id'];  // Use kid_id directly from table
        }
    }
    
    echo json_encode([
        'success' => true,
        'therapist_name' => $therapistName,
        'time_slots' => $timeSlots,
        'kids' => $kids,
        'grid' => $grid,
        'days' => $days
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
