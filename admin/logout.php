<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/auth.php';
session_start();
session_destroy();
header('Location: ' . SITE_URL . '/admin/login.php');
exit;
