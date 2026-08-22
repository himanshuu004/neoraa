<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT 
                            t.id,
                            t.therapist_id,
                            t.day_of_week,
                            t.time_slot,
                            t.kid_id,
                            t.session_type,
                            t.special_notes,
                            t.created_at,
                            t.updated_at,
                            u.username as therapist_username,
                            tp.name as therapist_name,
                            k.kid_name
                        FROM timetable t
                        LEFT JOIN users u ON t.therapist_id = u.id
                        LEFT JOIN therapist_profile tp ON u.id = tp.user_id
                        LEFT JOIN kids k ON t.kid_id = k.kid_id
                        ORDER BY 
                            FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
                            t.time_slot ASC");
    
    $timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $timetables,
        'count' => count($timetables)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
