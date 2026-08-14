<?php
/**
 * Fortunexdigital — Tag archive
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$pdo = DB::connect();
$stmt = $pdo->prepare("SELECT * FROM tags WHERE slug = ?");
$stmt->execute([$slug]);
$tag = $stmt->fetch();

if (!$tag) {
    header('HTTP/1.1 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$pageTitle = '#' . $tag['name'] . ' — ' . SITE_NAME;
$pageDescription = 'Articles tagged ' . $tag['name'] . ' on Fortunexdigital. Explore related money-making guides and side hustle ideas.';
$canonicalUrl = SITE_URL . '/tag/' . e($tag['slug']);

$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$total = $pdo->prepare("
    SELECT COUNT(*) FROM posts p
    JOIN post_tags pt ON pt.post_id = p.id
    WHERE pt.tag_id = ? AND p.status='published'
");
$total->execute([$tag['id']]);
$total = $total->fetchColumn();

$stmt = $pdo->prepare("
    SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at, c.name AS category_name, c.slug AS category_slug
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    JOIN post_tags pt ON pt.post_id = p.id
    WHERE pt.tag_id = ? AND p.status='published' ORDER BY p.published_at DESC LIMIT ? OFFSET ?
");
$stmt->execute([$tag['id'], $perPage, $offset]);
$posts = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= SITE_URL ?>/">Home</a><span class="sep">/</span>
        <a href="<?= SITE_URL ?>/blog">Blog</a><span class="sep">/</span>
        <span>#<?= e($tag['name']) ?></span>
    </nav>
</div>
<section class="section">
    <div class="container">
        <h1 class="section-title">Tag: <?= e($tag['name']) ?></h1>
        <p class="section-sub">All posts tagged with "<?= e($tag['name']) ?>".</p>
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
        <?= pagination($total, $perPage, $page, SITE_URL . '/tag/' . e($tag['slug'])) ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
