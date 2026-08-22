<?php
require_once '../../config/config.php';
requireAdmin();

$format = $_GET['format'] ?? 'csv';
$therapist_id = $_GET['therapist_id'] ?? null;

// Build query
$sql = "SELECT t.id, t.day_of_week, t.time_slot, t.client_name, 
               t.session_type, t.special_notes,
               tp.name as therapist_name, u.username
        FROM timetable t
        JOIN users u ON t.therapist_id = u.id
        LEFT JOIN therapist_profile tp ON u.id = tp.user_id";

$params = [];
if ($therapist_id) {
    $sql .= " WHERE t.therapist_id = ?";
    $params[] = $therapist_id;
}

$sql .= " ORDER BY t.day_of_week, t.time_slot";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="timetable_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Therapist', 'Day', 'Time', 'Client', 'Session Type', 'Notes']);
    
    foreach ($data as $row) {
        fputcsv($output, [
            $row['id'],
            $row['therapist_name'] ?: $row['username'],
            $row['day_of_week'],
            date('h:i A', strtotime($row['time_slot'])),
            $row['client_name'],
            $row['session_type'] ?: '',
            $row['special_notes'] ?: ''
        ]);
    }
    fclose($output);
    exit();
} elseif ($format === 'excel') {
    // Excel export is handled client-side using SheetJS
    // This endpoint can be used for server-side export if needed
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $data, 'message' => 'Use client-side export']);
    exit();
} elseif ($format === 'pdf') {
    // Simple PDF export using basic HTML to PDF conversion
    // For production, consider using TCPDF or FPDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="timetable_' . date('Y-m-d') . '.pdf"');
    
    $html = '<html><head><style>
        body { font-family: Arial; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style></head><body>';
    $html .= '<h2>Timetable Export - ' . date('Y-m-d') . '</h2>';
    $html .= '<table><tr><th>ID</th><th>Therapist</th><th>Day</th><th>Time</th><th>Client</th><th>Session Type</th><th>Notes</th></tr>';
    
    foreach ($data as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['therapist_name'] ?: $row['username']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['day_of_week']) . '</td>';
        $html .= '<td>' . date('h:i A', strtotime($row['time_slot'])) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['client_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['session_type'] ?: '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['special_notes'] ?: '') . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</table></body></html>';
    
    // Note: This is a simplified version. For proper PDF, use a library like TCPDF
    echo $html;
    exit();
}
?>
