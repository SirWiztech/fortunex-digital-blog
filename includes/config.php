<?php
/**
 * Fortunexdigital — Site configuration
 * Adjust these values to match your environment (XAMPP/WAMP/prod).
 */

// Load .env from the project root (dependency-free dotenv).
// Real environment variables take precedence over values in .env.
(function () {
    $envFile = __DIR__ . '/../.env';
    if (!is_file($envFile)) {
        return;
    }
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Strip surrounding quotes if present
        if (strlen($value) >= 2 && ($value[0] === '"' && substr($value, -1) === '"' || $value[0] === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        if ($key === '') {
            continue;
        }
        // Don't override real environment variables
        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
})();

define('SITE_NAME', 'Fortunexdigital');
define('SITE_TAGLINE', 'Affiliate Marketing Amplified');
define('SITE_URL', getenv('SITE_URL') ?: 'https://fortunexdigitals.com');
define('SITE_EMAIL', 'hello@fortunexdigital.com');

// Database credentials (read from environment variables, with local defaults)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'fortunexdigital');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// Paths
define('ROOT_DIR', __DIR__);
define('ASSETS', SITE_URL . '/assets');

// Admin
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', password_hash('fortunex2026', PASSWORD_DEFAULT));

// Cookie banner text
define('COOKIE_TEXT', 'We use cookies to improve your experience and show relevant ads (including Google AdSense). By continuing you agree to our Cookie Policy.');
