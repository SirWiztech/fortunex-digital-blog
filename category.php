<?php
/**
 * Fortunexdigital — Category archive
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$pdo = DB::connect();
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$stmt->execute([$slug]);
$cat = $stmt->fetch();

if (!$cat) {
    header('HTTP/1.1 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$pageTitle = $cat['name'] . ' — ' . SITE_NAME;
$pageDescription = 'Read the latest ' . $cat['name'] . ' articles on Fortunexdigital: ' . ($cat['description'] ?: 'practical guides to grow your online income.');
$canonicalUrl = SITE_URL . '/category/' . e($cat['slug']);

$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$total = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE category_id = ? AND status='published'");
$total->execute([$cat['id']]);
$total = $total->fetchColumn();

$stmt = $pdo->prepare("
    SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at, c.name AS category_name, c.slug AS category_slug
    FROM posts p LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.status='published' ORDER BY p.published_at DESC LIMIT ? OFFSET ?
");
$stmt->execute([$cat['id'], $perPage, $offset]);
$posts = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= SITE_URL ?>/">Home</a><span class="sep">/</span>
        <a href="<?= SITE_URL ?>/blog">Blog</a><span class="sep">/</span>
        <span><?= e($cat['name']) ?></span>
    </nav>
</div>
<section class="section">
    <div class="container">
        <div class="cat-hero">
            <img src="<?= e(category_image($cat)) ?>" alt="<?= e($cat['name']) ?> category" width="800" height="450">
        </div>
        <h1 class="section-title"><?= e($cat['name']) ?></h1>
        <p class="section-sub"><?= e($cat['description'] ?: 'Browse all ' . $cat['name'] . ' articles.') ?></p>
        <div class="post-grid">
            <?php foreach ($posts as $p): ?>
            <article class="post-card">
                <a class="thumb" href="<?= SITE_URL ?>/blog/<?= e($p['slug']) ?>"><?= post_thumb($p) ?></a>
                <div class="body">
                    <div class="meta"><?= format_date($p['published_at']) ?></div>
                    <h3><a href="<?= SITE_URL ?>/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                    <p class="muted"><?= e(excerpt($p['excerpt'] ?: $p['title'], 22)) ?></p>
                    <a class="read-more" href="<?= SITE_URL ?>/blog/<?= e($p['slug']) ?>">Read More »</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?= pagination($total, $perPage, $page, SITE_URL . '/category/' . e($cat['slug'])) ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
