<?php
/**
 * Setup Verification Script
 * Run this file once to verify your setup
 * Access: http://localhost/neora/setup.php or http://localhost/new/setup.php
 * Delete this file after setup is complete for security
 */
require_once __DIR__ . '/config/config.php';

// Check PHP version
echo "<h2>Neora Setup Verification</h2>";
echo "<h3>PHP Version Check</h3>";
echo "PHP Version: " . phpversion() . "<br>";
if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "<span style='color: green;'>✓ PHP version is compatible</span><br>";
} else {
    echo "<span style='color: red;'>✗ PHP 7.4 or higher is required</span><br>";
}

// Check PDO MySQL
echo "<h3>PDO MySQL Extension</h3>";
if (extension_loaded('pdo_mysql')) {
    echo "<span style='color: green;'>✓ PDO MySQL extension is loaded</span><br>";
} else {
    echo "<span style='color: red;'>✗ PDO MySQL extension is not loaded</span><br>";
}

// Test database connection
echo "<h3>Database Connection</h3>";
try {
    require_once 'config/database.php';
    echo "<span style='color: green;'>✓ Database connection successful</span><br>";
    
    // Check if tables exist
    $tables = ['users', 'roles', 'therapist_profile', 'timetable', 'sessions', 'kids', 'time_slots'];
    $allTablesExist = true;
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<span style='color: green;'>✓ Table '$table' exists</span><br>";
        } else {
            echo "<span style='color: red;'>✗ Table '$table' does not exist</span><br>";
            $allTablesExist = false;
        }
    }
    
    if ($allTablesExist) {
        // Check for admin user
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
        $result = $stmt->fetch();
        if ($result['count'] > 0) {
            echo "<span style='color: green;'>✓ Admin user exists</span><br>";
        } else {
            echo "<span style='color: orange;'>⚠ Admin user not found. Please run database.sql</span><br>";
        }
    }
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}

// Check file permissions
echo "<h3>File Structure</h3>";
$requiredDirs = ['admin', 'therapist', 'config', 'assets'];
foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        echo "<span style='color: green;'>✓ Directory '$dir' exists</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Directory '$dir' is missing</span><br>";
    }
}

echo "<hr>";
echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li>If database tables are missing, import database.sql in phpMyAdmin</li>";
echo "<li>Access the application at: <a href='" . htmlspecialchars(BASE_URL . 'login.php') . "'>Login Page</a></li>";
echo "<li>Default admin login: admin / admin123</li>";
echo "<li><strong>Delete this setup.php file after verification for security</strong></li>";
echo "</ol>";
?>
