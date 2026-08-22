<?php
/** Converted from core PHP, runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: application/json');

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS session_bookings (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255) NOT NULL,
    phone        VARCHAR(50)  NOT NULL,
    service      VARCHAR(255) DEFAULT NULL,
    message      TEXT         DEFAULT NULL,
    status       ENUM('new','reached_out','talked','closed') NOT NULL DEFAULT 'new',
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$name    = trim($_POST['name']    ?? '');
$phone   = trim($_POST['phone']   ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$phone) {
    echo json_encode(['success' => false, 'message' => 'Name and phone number are required.']);
    exit;
}

// Normalise phone, keep only digits for comparison
$phoneDigits = preg_replace('/\D/', '', $phone);
if (strlen($phoneDigits) < 7) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid phone number.']);
    exit;
}

// Duplicate check, compare stripped digits stored as-is or compare cleaned
$stmt = $pdo->prepare("SELECT id FROM session_bookings WHERE REGEXP_REPLACE(phone, '[^0-9]', '') = ? LIMIT 1");
$stmt->execute([$phoneDigits]);
if ($stmt->fetch()) {
    echo json_encode([
        'success'   => false,
        'duplicate' => true,
        'message'   => 'You have already submitted a booking request with this contact number. We will reach out to you soon, please wait for our call!'
    ]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO session_bookings (name, phone, service, message) VALUES (?, ?, ?, ?)"
);
$stmt->execute([$name, $phone, $service ?: null, $message ?: null]);

echo json_encode([
    'success' => true,
    'message' => 'Your booking request has been submitted! We will get back to you within 24 hours to confirm your appointment.'
]);
