<?php
require_once 'config/config.php';
requireAdmin();

// Test script to verify grid data
$therapist_id = $_GET['therapist_id'] ?? 5;

echo "<h2>Testing Grid Data for Therapist ID: $therapist_id</h2>";

// Get time slots
$timeSlotsStmt = $pdo->query("SELECT id, display_name FROM time_slots ORDER BY sort_order, time_start");
$timeSlots = $timeSlotsStmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Time Slots in Database:</h3>";
echo "<pre>";
print_r($timeSlots);
echo "</pre>";

// Get sessions
$sessionsStmt = $pdo->prepare("SELECT * FROM sessions WHERE therapist_id = ?");
$sessionsStmt->execute([$therapist_id]);
$sessions = $sessionsStmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Sessions in Database:</h3>";
echo "<pre>";
print_r($sessions);
echo "</pre>";

// Check matching
echo "<h3>Time Slot Matching Check:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Session Time Slot</th><th>Matches Display Name?</th></tr>";

foreach ($sessions as $session) {
    $matches = false;
    foreach ($timeSlots as $slot) {
        if ($slot['display_name'] === $session['time_slot']) {
            $matches = true;
            break;
        }
    }
    echo "<tr>";
    echo "<td>" . htmlspecialchars($session['time_slot']) . "</td>";
    echo "<td style='color: " . ($matches ? 'green' : 'red') . "'>" . ($matches ? 'YES ✓' : 'NO ✗') . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
