<?php
require_once '../../config/config.php';
requireAdmin();

$therapist_id = $_GET['therapist_id'] ?? 0;

if (empty($therapist_id)) {
    echo '<div class="alert alert-warning">Please select a therapist</div>';
    exit;
}

// Get therapist name
$stmt = $pdo->prepare("SELECT u.username, tp.name FROM users u LEFT JOIN therapist_profile tp ON u.id = tp.user_id WHERE u.id = ?");
$stmt->execute([$therapist_id]);
$therapist = $stmt->fetch();
$therapistName = $therapist ? ($therapist['name'] ?: $therapist['username']) : 'Unknown';

echo '<div class="mb-3"><h6><i class="fas fa-user-md"></i> Therapist: ' . htmlspecialchars($therapistName) . '</h6></div>';
echo '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Click any cell to assign or change a kid. Changes are saved automatically.</div>';

$is_editable = true;
include '../includes/dynamic_timetable_grid.php';
?>
