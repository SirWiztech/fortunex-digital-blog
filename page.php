<?php
/**
 * Fortunexdigital — Static pages (/p/<slug>) + Contact form handling
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';

// Contact form handling
if ($slug === 'contact' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_send'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');
    if ($name && $email && $msg) {
        try {
            $pdo = DB::connect();
            $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$name, $email, $msg]);
            $sent = true;
        } catch (Exception $e) { $sent = false; }
    }
}

$pdo = DB::connect();
$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page) {
    header('HTTP/1.1 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$pageTitle = $page['meta_title'] ?: ($page['title'] . ' — ' . SITE_NAME);
$pageDescription = $page['meta_description'] ?: excerpt($page['content'], 30);
$canonicalUrl = SITE_URL . '/p/' . e($page['slug']);

require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= SITE_URL ?>/">Home</a><span class="sep">/</span>
        <span><?= e($page['title']) ?></span>
    </nav>
</div>
<section class="section">
    <div class="container page-wrap">
        <h1><?= e($page['title']) ?></h1>

        <?php if ($slug === 'contact'): ?>
            <?php if (isset($sent) && $sent): ?>
                <p class="muted" style="background:var(--bg-soft);padding:14px 18px;border-radius:10px">Thanks, <?= e($name) ?>! We received your message and will reply soon.</p>
            <?php elseif (isset($sent) && !$sent): ?>
                <p class="muted" style="color:var(--accent)">Sorry, something went wrong. Please try again.</p>
            <?php endif; ?>
            <form class="contact" method="post" action="<?= SITE_URL ?>/p/contact">
                <label for="cname">Name</label>
                <input id="cname" type="text" name="name" required maxlength="80">
                <label for="cemail">Email</label>
                <input id="cemail" type="email" name="email" required maxlength="120">
                <label for="cmsg">Message</label>
                <textarea id="cmsg" name="message" rows="6" required></textarea>
                <p><button class="btn-cta" type="submit" name="contact_send">Send Message</button></p>
            </form>
        <?php else: ?>
            <div class="article-content">
                <?= $page['content'] ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
