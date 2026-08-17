<?php
/**
 * Fortunexdigital — Single post
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/comments.php';

$slug = $_GET['slug'] ?? '';
$post = $slug ? get_post($slug) : null;

if (!$post) {
    header('HTTP/1.1 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$pageTitle = ($post['meta_title'] ?: $post['title']) . ' | ' . SITE_NAME;
$pageDescription = $post['meta_description'] ?: excerpt($post['excerpt'] ?: $post['content'], 30);
$canonicalUrl = SITE_URL . '/blog/' . e($post['slug']);
$ogImage = !empty($post['featured_image']) ? $post['featured_image'] : ASSETS . '/img/og-default.svg';

$tags = get_tags_for_post($post['id']);
$related = get_related($post, 3);

$breadcrumbs = [
    'Home' => SITE_URL . '/',
    'Blog' => SITE_URL . '/blog',
    $post['category_name'] => SITE_URL . '/category/' . $post['category_slug'],
    $post['title'] => $canonicalUrl
];

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post['title'],
    'description' => $pageDescription,
    'image' => $ogImage,
    'datePublished' => $post['published_at'],
    'dateModified' => $post['updated_at'] ?? $post['published_at'],
    'author' => ['@type' => 'Person', 'name' => $post['author_name']],
    'publisher' => organization_jsonld(),
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
    'keywords' => implode(', ', array_column($tags, 'name'))
];
$jsonLd = [$jsonLd, breadcrumb_jsonld($breadcrumbs)];

require_once __DIR__ . '/includes/header.php';
track_visit();
?>
<div class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <?php
        $i = 0; $count = count($breadcrumbs);
        foreach ($breadcrumbs as $name => $url):
            if (++$i === $count) { echo '<span>' . e($name) . '</span>'; }
            else { echo '<a href="' . e($url) . '">' . e($name) . '</a><span class="sep">/</span>'; }
        endforeach;
        ?>
    </nav>

    <div class="post-layout" style="margin-top:20px">
        <article class="article">
            <?php if (!empty($post['featured_image'])): ?>
            <img class="post-hero-img" src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy" width="800" height="450">
            <?php else: ?>
            <div class="post-hero-img"><?= e($post['category_name']) ?></div>
            <?php endif; ?>
            <h1><?= e($post['title']) ?></h1>
            <div class="post-meta">By <?= e($post['author_name']) ?> · <?= format_date($post['published_at']) ?> · In <a href="<?= SITE_URL ?>/category/<?= e($post['category_slug']) ?>"><?= e($post['category_name']) ?></a></div>

            <div class="article-content">
                <?= $post['content'] /* trusted HTML from DB */ ?>
            </div>

            <div class="share-row">
                <span class="muted">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($canonicalUrl) ?>" rel="noopener">Facebook</a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($canonicalUrl) ?>&text=<?= urlencode($post['title']) ?>" rel="noopener">Twitter</a>
                <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($canonicalUrl) ?>" rel="noopener">Pinterest</a>
            </div>

            <?php if ($tags): ?>
            <div class="tag-row">
                <?php foreach ($tags as $t): ?>
                <a href="<?= SITE_URL ?>/tag/<?= e($t['slug']) ?>">#<?= e($t['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="post-author">
                <img src="<?= ASSETS ?>/img/author.svg" alt="<?= e($post['author_name']) ?>" loading="lazy" width="80" height="80">
                <div>
                    <h4><?= e($post['author_name']) ?></h4>
                    <p class="muted"><?= e($post['author_bio']) ?></p>
                </div>
            </div>

            <?php if ($related): ?>
            <section class="related">
                <h2>Related Posts</h2>
                <div class="post-grid">
                    <?php foreach ($related as $r): ?>
                    <article class="post-card">
                        <a class="thumb" href="<?= SITE_URL ?>/blog/<?= e($r['slug']) ?>"><?= post_thumb($r) ?></a>
                        <div class="body">
                            <h3><a href="<?= SITE_URL ?>/blog/<?= e($r['slug']) ?>"><?= e($r['title']) ?></a></h3>
                            <a class="read-more" href="<?= SITE_URL ?>/blog/<?= e($r['slug']) ?>">Read More »</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php comments_section($post['id']); ?>
        </article>

        <aside class="sidebar">
            <div class="widget widget-optin">
                <h4>Free Affiliate Course</h4>
                <p style="color:var(--muted);font-size:14px">Get our step-by-step video training.</p>
                <form action="<?= SITE_URL ?>/p/contact" method="get">
                    <input type="email" name="email" placeholder="Your email" required aria-label="Email">
                    <button class="btn-cta" type="submit">Get Access</button>
                </form>
            </div>
            <div class="widget">
                <h4>Popular Categories</h4>
                <ul>
                    <?php foreach (get_categories() as $c): ?>
                    <li><a href="<?= SITE_URL ?>/category/<?= e($c['slug']) ?>"><?= e($c['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
