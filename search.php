<?php
/**
 * Fortunexdigital — On-site search
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
$results = [];
if ($q !== '') {
    $pdo = DB::connect();
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare("
        SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at, c.name AS category_name
        FROM posts p LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status='published' AND (p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?)
        ORDER BY p.published_at DESC LIMIT 20
    ");
    $stmt->execute([$like, $like, $like]);
    $results = $stmt->fetchAll();
}

$pageTitle = ($q ? 'Search: ' . $q . ' — ' : 'Search — ') . SITE_NAME;
$pageDescription = 'Search Fortunexdigital for side hustles, affiliate marketing tips, and money-saving guides.';
$canonicalUrl = SITE_URL . '/search?q=' . urlencode($q);

require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Search the Blog</h1>
        <form class="search-form" method="get" action="<?= SITE_URL ?>/search">
            <div class="field">
                <label class="muted" for="q">Find an article</label>
                <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="e.g. apps that pay real money" required>
            </div>
            <button class="btn-cta" type="submit">Search</button>
        </form>

        <?php if ($q !== ''): ?>
            <p class="muted"><?= count($results) ?> result(s) for "<?= e($q) ?>"</p>
            <div class="search-results post-grid">
                <?php foreach ($results as $p): ?>
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
                <?php if (empty($results)): ?><p>No articles found. Try another keyword.</p><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
