<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS)) {
        $_SESSION['fxd_admin'] = true;
        header('Location: ' . SITE_URL . '/admin/index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login — <?= SITE_NAME ?></title>
<link rel="stylesheet" href="<?= ASSETS ?>/css/style.css">
</head>
<body>
<a class="skip-link" href="#content">Skip</a>
<header class="site-header"><div class="container header-inner">
  <a class="brand" href="<?= SITE_URL ?>/"><img src="<?= logo_url() ?>" alt="logo" height="40"></a>
  <a href="<?= SITE_URL ?>/">← Back to site</a>
</div></header>
<main id="content" class="section"><div class="container" style="max-width:420px">
  <h1>Admin Login</h1>
  <?php if ($error): ?><p style="color:var(--accent)"><?= e($error) ?></p><?php endif; ?>
  <form class="contact" method="post" action="">
    <label>Username</label>
    <input type="text" name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <p><button class="btn-cta" type="submit">Log In</button></p>
  </form>
  <p class="muted">Default credentials: <code>admin</code> / <code>fortunex2026</code> (change in includes/config.php).</p>
</div></main>
</body>
</html>
