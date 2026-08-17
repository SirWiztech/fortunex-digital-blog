<?php
/**
 * Fortunexdigital — 404 error page
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
if (!headers_sent()) header('HTTP/1.1 404 Not Found');
$pageTitle = 'Page Not Found (404) — ' . SITE_NAME;
$pageDescription = 'The page you were looking for could not be found on Fortunexdigital.';
require_once __DIR__ . '/includes/header.php';
track_visit();
?>
<section class="notfound">
    <div class="container">
        <h1>404</h1>
        <h2>Oops! This page took a different money move.</h2>
        <p class="muted">The page you requested doesn't exist or may have moved.</p>
        <p>
            <a class="btn-cta" href="<?= SITE_URL ?>/">Back to Home</a>
            <a class="btn-outline" href="<?= SITE_URL ?>/blog" style="margin-left:10px">Browse the Blog</a>
        </p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
