<?php
/**
 * Fortunexdigital — Admin auth helper
 */
require_once __DIR__ . '/../includes/config.php';

session_start();

function admin_logged_in() {
    return !empty($_SESSION['fxd_admin']);
}

function require_admin() {
    if (!admin_logged_in()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}
