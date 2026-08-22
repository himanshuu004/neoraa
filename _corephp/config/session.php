<?php
// Use cookie path '/' so session is sent for all paths (avoids redirect loop when app is under /new/ or /neora/)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check if user is admin (case-insensitive for role)
function isAdmin() {
    return isLoggedIn() && strtolower((string)$_SESSION['role']) === 'admin';
}

// Check if user is therapist
function isTherapist() {
    return isLoggedIn() && strtolower((string)$_SESSION['role']) === 'therapist';
}

// Check if user is coordinator
function isCoordinator() {
    return isLoggedIn() && strtolower((string)$_SESSION['role']) === 'coordinator';
}

// Require admin or coordinator (for application.php etc.)
function requireAdminOrCoordinator() {
    requireLogin();
    if (!isAdmin() && !isCoordinator()) {
        // Add redirected parameter to prevent loops
        $loginUrl = BASE_URL . 'login.php';
        if (strpos($loginUrl, '?') === false) {
            $loginUrl .= '?redirected=1';
        } else {
            $loginUrl .= '&redirected=1';
        }
        header('Location: ' . $loginUrl);
        exit();
    }
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        // Add redirected parameter to prevent loops
        $loginUrl = BASE_URL . 'login.php';
        if (strpos($loginUrl, '?') === false) {
            $loginUrl .= '?redirected=1';
        } else {
            $loginUrl .= '&redirected=1';
        }
        header('Location: ' . $loginUrl);
        exit();
    }
}

// Require admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        // Add redirected parameter to prevent loops
        $loginUrl = BASE_URL . 'login.php';
        if (strpos($loginUrl, '?') === false) {
            $loginUrl .= '?redirected=1';
        } else {
            $loginUrl .= '&redirected=1';
        }
        header('Location: ' . $loginUrl);
        exit();
    }
}

// Require therapist
function requireTherapist() {
    requireLogin();
    if (!isTherapist()) {
        // Add redirected parameter to prevent loops
        $loginUrl = BASE_URL . 'login.php';
        if (strpos($loginUrl, '?') === false) {
            $loginUrl .= '?redirected=1';
        } else {
            $loginUrl .= '&redirected=1';
        }
        header('Location: ' . $loginUrl);
        exit();
    }
}

// Check if user is trainee
function isTrainee() {
    return isLoggedIn() && strtolower((string)$_SESSION['role']) === 'trainee';
}

// Require trainee
function requireTrainee() {
    requireLogin();
    if (!isTrainee()) {
        // Add redirected parameter to prevent loops
        $loginUrl = BASE_URL . 'login.php';
        if (strpos($loginUrl, '?') === false) {
            $loginUrl .= '?redirected=1';
        } else {
            $loginUrl .= '&redirected=1';
        }
        header('Location: ' . $loginUrl);
        exit();
    }
}
?>