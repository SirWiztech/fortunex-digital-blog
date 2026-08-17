<?php
/**
 * Fortunexdigital — Blog listing (pagination)
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Blog — Money-Making Guides | ' . SITE_NAME;
$pageDescription = 'Browse Fortunexdigital\'s blog for proven side hustles, affiliate marketing tips, and smart saving strategies to build real online income.';
$canonicalUrl = SITE_URL . '/blog';

$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$pdo = DB::connect();
$total = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$stmt = $pdo->prepare("
    SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at, c.slug AS category_slug, c.name AS category_name
    FROM posts p LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'published' ORDER BY p.published_at DESC LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$posts = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
track_visit();
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Recent Articles</h1>
        <p class="section-sub">Fresh, tried-and-tested ways to make and save money online.</p>

        <div class="post-grid">
            <?php foreach ($posts as $p): ?>
            <article class="post-card">
                <a class="thumb" href="<?= SITE_URL ?>/blog/<?= e($p['slug']) ?>"><?= post_thumb($p) ?></a>
                <div class="body">
                    <div class="meta"><?= e($p['category_name']) ?> · <?= format_date($p['published_at']) ?></div>
                    <h3><a href="<?= SITE_URL ?>/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                    <p class="muted"><?= e(excerpt($p['excerpt'] ?: $p['title'], 22)) ?></p>
                    <a class="read-more" href="<?= SITE_URL ?>/blog/<?= e($p['slug']) ?>">Read More »</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?= pagination($total, $perPage, $page, SITE_URL . '/blog') ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
