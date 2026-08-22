<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = trim($_POST['status'] ?? '');
$note = trim($_POST['note'] ?? '');

if ($id < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
    exit;
}

$allowed_statuses = ['New', 'Shortlisted', 'Interview Scheduled', 'Rejected', 'Hired'];
if ($status !== '' && !in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Ensure status and note columns exist
    try {
        $pdo->exec("ALTER TABLE hiring_applications ADD COLUMN status VARCHAR(50) DEFAULT 'New'");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') === false) throw $e;
    }
    try {
        $pdo->exec("ALTER TABLE hiring_applications ADD COLUMN note TEXT DEFAULT NULL");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') === false) throw $e;
    }

    if ($status === '') {
        $stmt = $pdo->prepare("SELECT status FROM hiring_applications WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $status = $row['status'] ?? 'New';
    }
    $stmt = $pdo->prepare("UPDATE hiring_applications SET status = ?, note = ? WHERE id = ?");
    $stmt->execute([$status, $note, $id]);

    if ($stmt->rowCount() >= 0) {
        echo json_encode(['success' => true, 'message' => 'Updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Application not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
