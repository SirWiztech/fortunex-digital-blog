<?php
/**
 * Fortunexdigital — Site configuration
 * Adjust these values to match your environment (XAMPP/WAMP/prod).
 */
define('SITE_NAME', 'Fortunexdigital');
define('SITE_TAGLINE', 'Affiliate Marketing Amplified');
define('SITE_URL', 'http://localhost/fortunex-digital-blog');
define('SITE_EMAIL', 'hello@fortunexdigital.com');

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'fortunexdigital');
define('DB_USER', 'root');
define('DB_PASS', '');

// Paths
define('ROOT_DIR', __DIR__);
define('ASSETS', SITE_URL . '/assets');

// Admin
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', password_hash('fortunex2026', PASSWORD_DEFAULT));

// Cookie banner text
define('COOKIE_TEXT', 'We use cookies to improve your experience and show relevant ads (including Google AdSense). By continuing you agree to our Cookie Policy.');
