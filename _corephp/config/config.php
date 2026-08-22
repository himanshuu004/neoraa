<?php
// Base configuration: detect app base path from request URI so /neora/ and /new/ both work locally
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$uri = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '';
$isLocal = ($host === 'localhost' || strpos($host, '127.0.0.1') !== false);
if ($isLocal && preg_match('#^/(neora|new)(/|$)#', $uri, $m)) {
    define('BASE_URL', '/' . $m[1] . '/');
} else {
    define('BASE_URL', '/');
}
define('SITE_NAME', 'Neora Therapy Management');

// Include database and session
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';
// Note: trainee_schema.php is included only when needed (in trainee/admin pages)
?>