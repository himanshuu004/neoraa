<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

header('Content-Type: text/html; charset=UTF-8');

$therapist_id = (int) ($_GET['therapist_id'] ?? 0);

if ($therapist_id < 1) {
    echo '<div class="alert alert-warning">Please select a therapist</div>';
    return;
}

try {
    $stmt = $pdo->prepare("SELECT u.username, tp.name FROM users u LEFT JOIN therapist_profile tp ON u.id = tp.user_id WHERE u.id = ?");
    $stmt->execute([$therapist_id]);
    $therapist = $stmt->fetch(PDO::FETCH_ASSOC);
    $therapistName = $therapist ? ($therapist['name'] ?: $therapist['username']) : 'Unknown';

    echo '<div class="mb-3"><h6><i class="fas fa-user-md"></i> Therapist: ' . htmlspecialchars((string) $therapistName) . '</h6></div>';
    echo '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Click any cell to assign or change a kid. Changes are saved automatically.</div>';

    $is_editable = true;
    echo view('admin.partials.dynamic_timetable_grid', [
        'pdo' => $pdo,
        'therapist_id' => $therapist_id,
        'is_editable' => true,
    ])->render();
} catch (Throwable $e) {
    echo '<div class="alert alert-danger">Error loading timetable grid: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
