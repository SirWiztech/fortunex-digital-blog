<?php
/**
 * Fortunexdigital — Dynamic XML sitemap
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/xml; charset=utf-8');

$pdo = DB::connect();
$urls = [];

// Home
$urls[] = ['loc' => SITE_URL . '/', 'priority' => '1.0', 'changefreq' => 'daily'];

// Blog index
$urls[] = ['loc' => SITE_URL . '/blog', 'priority' => '0.9', 'changefreq' => 'daily'];

// Categories
$catRows = $pdo->query("SELECT slug FROM categories")->fetchAll();
foreach ($catRows as $c) {
    $urls[] = ['loc' => SITE_URL . '/category/' . $c['slug'], 'priority' => '0.7', 'changefreq' => 'weekly'];
}

// Tags
$tagRows = $pdo->query("SELECT slug FROM tags")->fetchAll();
foreach ($tagRows as $t) {
    $urls[] = ['loc' => SITE_URL . '/tag/' . $t['slug'], 'priority' => '0.5', 'changefreq' => 'weekly'];
}

// Posts
$postRows = $pdo->query("SELECT slug, updated_at, published_at FROM posts WHERE status='published'")->fetchAll();
foreach ($postRows as $p) {
    $lastmod = !empty($p['updated_at']) ? $p['updated_at'] : $p['published_at'];
    $urls[] = ['loc' => SITE_URL . '/blog/' . $p['slug'], 'lastmod' => date('c', strtotime($lastmod)), 'priority' => '0.8', 'changefreq' => 'weekly'];
}

// Static pages
$pageRows = $pdo->query("SELECT slug, updated_at FROM pages")->fetchAll();
foreach ($pageRows as $pg) {
    $lastmod = !empty($pg['updated_at']) ? $pg['updated_at'] : date('Y-m-d');
    $urls[] = ['loc' => SITE_URL . '/p/' . $pg['slug'], 'lastmod' => date('c', strtotime($lastmod)), 'priority' => '0.6', 'changefreq' => 'monthly'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc>' . "\n";
    if (!empty($u['lastmod'])) echo '    <lastmod>' . htmlspecialchars($u['lastmod'], ENT_XML1) . '</lastmod>' . "\n";
    if (!empty($u['changefreq'])) echo '    <changefreq>' . $u['changefreq'] . '</changefreq>' . "\n";
    if (!empty($u['priority'])) echo '    <priority>' . $u['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}
echo '</urlset>';
