<?php
/**
 * Fortunexdigital — PDO database connection
 */
require_once __DIR__ . '/config.php';

class DB {
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // Friendly message when DB is not set up yet
                if (php_sapi_name() !== 'cli') {
                    http_response_code(503);
                    echo '<!doctype html><html><head><meta charset="utf-8"><title>Setup required</title>';
                    echo '<body style="font-family:sans-serif;max-width:600px;margin:80px auto;padding:0 20px">';
                    echo '<h1>Fortunexdigital — Database not configured</h1>';
                    echo '<p>The site cannot connect to the MySQL database <code>' . DB_NAME . '</code>.</p>';
                    echo '<p>1. Create the database in phpMyAdmin (or run <code>CREATE DATABASE fortunexdigital;</code>).<br>';
                    echo '2. Visit <a href="' . SITE_URL . '/install.php">/install.php</a> to build tables and seed content.</p>';
                    echo '<p><small>' . htmlspecialchars($e->getMessage()) . '</small></p>';
                    echo '</body></html>';
                    exit;
                }
                throw $e;
            }
        }
        return self::$pdo;
    }
}
