<?php
/**
 * Creates trainee-related tables if they don't exist.
 * Include this file once (e.g., from admin dashboard or setup script).
 * This file is safe to include multiple times - it uses IF NOT EXISTS.
 */
if (!isset($pdo)) {
    return;
}

try {
    // Ensure trainee role exists
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles WHERE role_name = 'trainee'");
        $roleCheck = $stmt->fetch();
        
        if ($roleCheck['count'] == 0) {
            $pdo->exec("INSERT INTO roles (role_name) VALUES ('trainee')");
        }
    } catch (PDOException $e) {
        // Roles table might not exist yet, that's okay
        error_log("Trainee role check error: " . $e->getMessage());
    }
    
    // Create trainee_profile table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS trainee_profile (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            contact VARCHAR(20) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            profile_image VARCHAR(500) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (PDOException $e) {
        // Table might already exist or foreign key constraint issue
        // Try without foreign key if it fails
        if (strpos($e->getMessage(), 'already exists') === false) {
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS trainee_profile (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT UNSIGNED NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    contact VARCHAR(20) DEFAULT NULL,
                    email VARCHAR(255) DEFAULT NULL,
                    profile_image VARCHAR(500) DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_user_id (user_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            } catch (PDOException $e2) {
                error_log("Trainee profile table creation error: " . $e2->getMessage());
            }
        }
    }
    
    // Create trainee_attendance table for session attendance
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS trainee_attendance (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            trainee_id INT UNSIGNED NOT NULL,
            session_date DATE NOT NULL,
            child_name VARCHAR(255) NOT NULL,
            activity_description TEXT NOT NULL,
            session_image VARCHAR(500) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (trainee_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_trainee_date (trainee_id, session_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (PDOException $e) {
        // Table might already exist or foreign key constraint issue
        // Try without foreign key if it fails
        if (strpos($e->getMessage(), 'already exists') === false) {
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS trainee_attendance (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    trainee_id INT UNSIGNED NOT NULL,
                    session_date DATE NOT NULL,
                    child_name VARCHAR(255) NOT NULL,
                    activity_description TEXT NOT NULL,
                    session_image VARCHAR(500) DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_trainee_date (trainee_id, session_date)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            } catch (PDOException $e2) {
                error_log("Trainee attendance table creation error: " . $e2->getMessage());
            }
        }
    }
    
    // Ensure session_image column exists in trainee_attendance table (for backward compatibility)
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM trainee_attendance LIKE 'session_image'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE trainee_attendance ADD COLUMN session_image VARCHAR(500) DEFAULT NULL AFTER activity_description");
        }
    } catch (PDOException $e) {
        error_log("Error adding session_image column: " . $e->getMessage());
    }
    
    // Create trainee_session_images table for multiple images per session
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS trainee_session_images (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            attendance_id INT UNSIGNED NOT NULL,
            image_path VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (attendance_id) REFERENCES trainee_attendance(id) ON DELETE CASCADE,
            INDEX idx_attendance_id (attendance_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (PDOException $e) {
        // Table might already exist or foreign key constraint issue
        // Try without foreign key if it fails
        if (strpos($e->getMessage(), 'already exists') === false) {
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS trainee_session_images (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    attendance_id INT UNSIGNED NOT NULL,
                    image_path VARCHAR(500) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_attendance_id (attendance_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            } catch (PDOException $e2) {
                error_log("Trainee session images table creation error: " . $e2->getMessage());
            }
        }
    }
    
} catch (Exception $e) {
    // Log error but don't break execution
    error_log("Trainee schema error: " . $e->getMessage());
}
