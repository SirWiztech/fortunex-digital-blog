<?php
/**
 * Fortunexdigital — Homepage (replica of Loud Money Moves layout)
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = SITE_NAME . ' — ' . SITE_TAGLINE;
$pageDescription = 'Fortunexdigital shares proven ways to make money online, build affiliate income, and save smarter with real, tested side hustles and business ideas.';
$canonicalUrl = SITE_URL . '/';

// Popular posts (most recent published)
$pdo = DB::connect();
$popular = $pdo->query("
    SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at, c.slug AS category_slug, c.name AS category_name
    FROM posts p LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'published' ORDER BY p.published_at DESC LIMIT 3
")->fetchAll();

$cats = get_categories();

require_once __DIR__ . '/includes/header.php';
track_visit();
?>
<section class="hero">
    <div class="container">
        <h1>A Free Course in Building<br>an Affiliate Site That Actually Pays</h1>
        <p>The exact, step-by-step system behind every income guide on this site — recorded on video, no experience required to start.</p>
        <form class="optin" action="<?= SITE_URL ?>/p/contact" method="get">
            <input type="email" name="email" placeholder="Enter your email address" required aria-label="Email">
            <button type="submit" class="btn-cta">Send Me the Course</button>
        </form>
    </div>
</section>

<div class="featured-on">
    <div class="container">
        <h4>As Covered By</h4>
        <div class="logo-strip">
            <span>Niche Pursuits</span>
            <span>A Better Lemonade Stand</span>
            <span>Web Retailer</span>
            <span>Side Hustle Nation</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="author-box">
            <img src="<?= ASSETS ?>/img/author.svg" alt="Founder of Fortunexdigital" loading="lazy" width="110" height="110">
            <div>
                <span class="eyebrow">Signed</span>
                <h3>Alex, Founder</h3>
                <p>I write down what actually worked — the side hustles, the affiliate plays, the mistakes — so you can skip the guesswork.</p>
                <a class="btn-outline" href="<?= SITE_URL ?>/p/about">Read the Full Story</a>
            </div>
        </div>
    </div>
</section>

<section class="section soft">
    <div class="container">
        <span class="eyebrow">Ledger — Category Index</span>
        <h2 class="section-title">Pick a Line Item to Open</h2>
        <p class="section-sub">Every guide on this site files under one of these. Start wherever your income gap is.</p>
        <div class="cat-grid">
            <?php foreach ($cats as $c): ?>
            <a class="cat-card" href="<?= SITE_URL ?>/category/<?= e($c['slug']) ?>">
                <div class="thumb"><img src="<?= ASSETS ?>/img/categories/<?= e($c['slug']) ?>.jpg" alt="<?= e($c['name']) ?> category" loading="lazy"></div>
                <div class="body"><h3><?= e($c['name']) ?> &rarr;</h3></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="training">
    <div class="container">
        <span class="eyebrow">Weekly Entry</span>
        <h2>New Tutorial Every Week</h2>
        <p>Subscribe on YouTube and each breakdown lands in your feed the day it's filmed — no course, no upsell.</p>
        <a class="btn-cta" href="https://www.youtube.com/@fortunexdigital?sub_confirmation=1" rel="noopener">Subscribe on YouTube</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <span class="eyebrow">Most Read Entries</span>
        <h2 class="section-title">Latest From the Ledger</h2>
        <p class="section-sub">The guides readers keep coming back to — real numbers, real tests, nothing theoretical.</p>
        <div class="post-grid">
            <?php foreach ($popular as $p): ?>
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
    </div>
</section>

<section class="cta-banner">
    <div class="container">
        <div>
            <h2>Ready to Open Your Own Line Item?</h2>
            <p>Join the readers building real, tracked income on the side — one tested idea at a time.</p>
            <a class="btn-cta" href="<?= SITE_URL ?>/p/sign-up-offers">Show Me the System</a>
        </div>
        <div class="stamp">Fortunex<br>Verified</div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>