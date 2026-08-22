<?php
/**
 * Setup script to create trainee tables
 * Run this once: http://localhost/new/setup_trainee_tables.php
 * Delete this file after use for security
 */

require_once 'config/config.php';
require_once 'config/trainee_schema.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trainee Tables Setup</title>
    <style>
        body { font-family: Arial; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Trainee Tables Setup</h1>
    
    <?php
    try {
        // Check if tables exist
        $tables = ['trainee_profile', 'trainee_attendance'];
        $allExist = true;
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='success'>✓ Table '$table' exists</div>";
            } else {
                echo "<div class='error'>✗ Table '$table' does not exist</div>";
                $allExist = false;
            }
        }
        
        // Check trainee role
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles WHERE role_name = 'trainee'");
        $roleCheck = $stmt->fetch();
        if ($roleCheck['count'] > 0) {
            echo "<div class='success'>✓ Trainee role exists</div>";
        } else {
            echo "<div class='error'>✗ Trainee role does not exist</div>";
        }
        
        // Force table creation by including schema again
        require_once 'config/trainee_schema.php';
        
        // Check again
        echo "<h2>After Schema Execution:</h2>";
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='success'>✓ Table '$table' now exists</div>";
            } else {
                echo "<div class='error'>✗ Table '$table' still does not exist</div>";
            }
        }
        
        // Check trainee role again
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles WHERE role_name = 'trainee'");
        $roleCheck = $stmt->fetch();
        if ($roleCheck['count'] > 0) {
            echo "<div class='success'>✓ Trainee role now exists</div>";
        } else {
            echo "<div class='error'>✗ Trainee role still does not exist</div>";
        }
        
        echo "<div class='info'><strong>Setup complete!</strong> You can now delete this file for security.</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>
    
    <p><a href="login.php">Go to Login Page</a></p>
</body>
</html>
