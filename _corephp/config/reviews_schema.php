<?php
/**
 * Ensures reviews table exists. Include once (e.g. from admin/reviews.php or API).
 */
if (!isset($pdo)) {
    return;
}
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `text` TEXT NOT NULL,
        author VARCHAR(255) NOT NULL,
        `location` VARCHAR(255) DEFAULT NULL,
        photo_path VARCHAR(500) DEFAULT NULL,
        display_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
    if ($stmt && (int) $stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO reviews (`text`, author, `location`, display_order) VALUES
            ('As parents, we were anxious in the beginning. But meeting Priyanka Ma''am gave us confidence. She is very kind, very patient, and truly understands my daughter.', 'Priya Sharma', 'Dehradun', 0),
            ('Initially we didn''t understand how to tackle the issues with my daughter because she was not able to communicate with us but Priyanka maam explain the things , make us understood about Autism spectrum,gave us knowledge.', 'Rajesh Kumar', 'Dehradun', 1),
            ('Her work on pre-literacy and basic understanding skills has helped our child gain confidence step by step.', 'Anita Singh', 'Dehradun', 2),
            ('We feel lucky to have found such a dedicated speech therapist in Dehradun. Thankyou so much Priyanka Maam ,god bless you and the team with the best ..', 'Vikram Mehta', 'Dehradun', 3)");
    }
} catch (PDOException $e) {
    // ignore if already exists
}
?>
