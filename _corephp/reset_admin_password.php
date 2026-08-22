<?php
/**
 * Reset Admin Password Script
 * Run this once to set/reset admin password to 'admin123'
 * Access: http://localhost/Neora/reset_admin_password.php
 * Delete this file after use for security
 */

require_once 'config/config.php';

$newPassword = 'admin123';
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

echo "<!DOCTYPE html><html><head><title>Reset Admin Password</title>";
echo "<style>body{font-family:Arial;padding:20px;max-width:600px;margin:0 auto;}";
echo ".success{color:green;background:#d4edda;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".error{color:red;background:#f8d7da;padding:15px;border-radius:5px;margin:10px 0;}";
echo "a{color:#007bff;text-decoration:none;} a:hover{text-decoration:underline;}";
echo "</style></head><body>";

try {
    // First, ensure roles exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles WHERE role_name = 'admin'");
    $roleCheck = $stmt->fetch();
    
    if ($roleCheck['count'] == 0) {
        // Create roles if they don't exist
        $pdo->exec("INSERT INTO roles (role_name) VALUES ('admin'), ('therapist')");
        echo "<div class='success'>✓ Created admin and therapist roles</div>";
    }
    
    // Get admin role ID
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = 'admin'");
    $stmt->execute();
    $role = $stmt->fetch();
    
    if (!$role) {
        throw new Exception("Could not find or create admin role");
    }
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Update existing admin password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $stmt->execute([$hashedPassword]);
        echo "<div class='success'>";
        echo "<h2>✓ Admin password reset successfully!</h2>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><a href='" . htmlspecialchars(BASE_URL . 'login.php') . "'>→ Go to Login Page</a></p>";
        echo "</div>";
    } else {
        // Create admin user if doesn't exist
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES ('admin', ?, ?)");
        $stmt->execute([$hashedPassword, $role['id']]);
        echo "<div class='success'>";
        echo "<h2>✓ Admin user created successfully!</h2>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><a href='" . htmlspecialchars(BASE_URL . 'login.php') . "'>→ Go to Login Page</a></p>";
        echo "</div>";
    }
    
    // Verify the password works
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = 'admin'");
    $stmt->execute();
    $verifyUser = $stmt->fetch();
    if ($verifyUser && password_verify('admin123', $verifyUser['password'])) {
        echo "<div class='success'>✓ Password verification test passed!</div>";
    } else {
        echo "<div class='error'>✗ Password verification test failed. Please try again.</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>✗ Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>Database 'neora_db' exists</li>";
    echo "<li>You have run database.sql</li>";
    echo "<li>Database connection is working (check config/database.php)</li>";
    echo "</ul>";
    echo "<p><a href='" . htmlspecialchars(BASE_URL . 'login.php') . "'>Go to Login</a></p>";
    echo "</div>";
}

echo "</body></html>";
?>
