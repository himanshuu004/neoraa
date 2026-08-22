<?php
require_once 'config/config.php';
requireAdmin();

$admin_id = $_SESSION['user_id'];

echo "<h2>Admin Session Debug</h2>";
echo "<p><strong>Admin ID:</strong> $admin_id</p>";

// Check user role
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$user = $stmt->fetch();
echo "<p><strong>User Role:</strong> " . $user['role'] . "</p>";
echo "<p><strong>Username:</strong> " . $user['username'] . "</p>";

// Check existing sessions
$stmt = $pdo->prepare("SELECT * FROM sessions WHERE therapist_id = ? ORDER BY day_of_week, time_slot");
$stmt->execute([$admin_id]);
$sessions = $stmt->fetchAll();

echo "<h3>Existing Sessions for Admin (Total: " . count($sessions) . ")</h3>";

if (count($sessions) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr>
            <th>ID</th>
            <th>Day</th>
            <th>Time Slot</th>
            <th>Kid ID</th>
            <th>Client Name</th>
            <th>Special Notes</th>
            <th>Created At</th>
          </tr>";
    
    foreach ($sessions as $session) {
        echo "<tr>";
        echo "<td>" . $session['id'] . "</td>";
        echo "<td>" . $session['day_of_week'] . "</td>";
        echo "<td>" . $session['time_slot'] . "</td>";
        echo "<td>" . ($session['kid_id'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($session['client_name'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($session['special_notes'] ?? '') . "</td>";
        echo "<td>" . $session['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠ No sessions found for this admin</p>";
}

// Check sessions table structure
echo "<h3>Sessions Table Structure</h3>";
$stmt = $pdo->query("DESCRIBE sessions");
$columns = $stmt->fetchAll();

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    echo "<tr>";
    echo "<td>" . $col['Field'] . "</td>";
    echo "<td>" . $col['Type'] . "</td>";
    echo "<td>" . $col['Null'] . "</td>";
    echo "<td>" . $col['Key'] . "</td>";
    echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test insert
echo "<h3>Test Insert</h3>";
try {
    $testStmt = $pdo->prepare("INSERT INTO sessions (therapist_id, day_of_week, time_slot, kid_id, client_name, session_type, special_notes) 
                               VALUES (?, 'Monday', '10:00 AM - 10:45 AM', 1, 'Test Kid', 'Test Session', 'Test Note')");
    $testStmt->execute([$admin_id]);
    echo "<p style='color: green;'>✓ Test insert successful! Session ID: " . $pdo->lastInsertId() . "</p>";
    
    // Delete test insert
    $pdo->prepare("DELETE FROM sessions WHERE id = ?")->execute([$pdo->lastInsertId()]);
    echo "<p style='color: blue;'>✓ Test data cleaned up</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Test insert failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/my_session.php'>← Back to My Session</a></p>";
?>
