<?php
/**
 * Ensures hiring_applications table exists with all required columns.
 * Safe to include multiple times — uses CREATE TABLE IF NOT EXISTS
 * and ALTER TABLE with duplicate-column guard.
 */
if (!isset($pdo)) return;

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS hiring_applications (
        id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name             VARCHAR(255) NOT NULL,
        mobile           VARCHAR(20)  NOT NULL,
        email            VARCHAR(255) NOT NULL,
        city             VARCHAR(100) DEFAULT NULL,
        applying_for     VARCHAR(255) DEFAULT NULL,
        qualification    VARCHAR(255) DEFAULT NULL,
        college          VARCHAR(255) DEFAULT NULL,
        year             VARCHAR(10)  DEFAULT NULL,
        experience_type  VARCHAR(50)  DEFAULT NULL,
        experience_years VARCHAR(20)  DEFAULT NULL,
        current_place    VARCHAR(255) DEFAULT NULL,
        areas_specialization TEXT     DEFAULT NULL,
        languages        VARCHAR(255) DEFAULT NULL,
        joining_time     VARCHAR(100) DEFAULT NULL,
        resume_path      VARCHAR(500) DEFAULT NULL,
        certificate_path VARCHAR(500) DEFAULT NULL,
        why_join_neora   TEXT         DEFAULT NULL,
        status           VARCHAR(50)  NOT NULL DEFAULT 'New',
        note             TEXT         DEFAULT NULL,
        generated_by     INT UNSIGNED DEFAULT NULL,
        created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        updated_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (PDOException $e) {
    // table already exists — ignore
}

// Ensure any columns that may be missing from older installs
$extraCols = [
    "ALTER TABLE hiring_applications ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'New'",
    "ALTER TABLE hiring_applications ADD COLUMN note TEXT DEFAULT NULL",
    "ALTER TABLE hiring_applications ADD COLUMN generated_by INT UNSIGNED DEFAULT NULL",
    "ALTER TABLE hiring_applications ADD COLUMN certificate_path VARCHAR(500) DEFAULT NULL",
    "ALTER TABLE hiring_applications ADD COLUMN why_join_neora TEXT DEFAULT NULL",
];
foreach ($extraCols as $sql) {
    try { $pdo->exec($sql); } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') === false) { /* ignore */ }
    }
}
?>
