<?php

/**
 * Lindr Platform Shared Hosting Front Controller Gateway
 * Automatically proxies requests to public/index.php so the site runs out of the box
 * on any cPanel / DirectAdmin shared host without directory index listings.
 */

define('LARAVEL_START', microtime(true));

// Check if request is for setup.php directly
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');

if (strpos($uri, 'setup.php') !== false) {
    if (file_exists(__DIR__ . '/setup.php')) {
        require __DIR__ . '/setup.php';
        exit;
    }
}

// Forward to public/index.php controller
require_once __DIR__ . '/public/index.php';
