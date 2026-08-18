<?php
/**
 * Fortunexdigital — site header (semantic <head> + <header>/<nav>)
 * Expects globals: $pageTitle, $pageDescription, $canonicalUrl, $ogImage, $jsonLd, $bodyClass
 */
global $pageTitle, $pageDescription, $canonicalUrl, $ogImage, $jsonLd, $bodyClass;

$pageTitle = $pageTitle ?? SITE_NAME . ' — ' . SITE_TAGLINE;
$pageDescription = $pageDescription ?? 'Fortunexdigital shares proven ways to make money online, save smarter, and build a profitable affiliate business.';
// Canonical URL fallback: SITE_URL already includes any base path, so strip
// it from the request URI to avoid doubling it (e.g. on subfolder localhost installs).
$requestPath = $_SERVER['REQUEST_URI'] ?? '/';
if (defined('BASE_PATH') && BASE_PATH !== '' && strpos($requestPath, BASE_PATH) === 0) {
    $requestPath = substr($requestPath, strlen(BASE_PATH));
    if ($requestPath === '' || $requestPath === false) {
        $requestPath = '/';
    }
}
$canonicalUrl = $canonicalUrl ?? (SITE_URL . $requestPath);
$ogImage = $ogImage ?? ASSETS . '/img/og-default.svg';

// Build JSON-LD blocks: always include Organization, then page-specific block(s)
$ldBlocks = [organization_jsonld()];
if (!empty($jsonLd)) {
    if (isset($jsonLd[0]) && is_array($jsonLd[0])) {
        $ldBlocks = array_merge($ldBlocks, $jsonLd);
    } else {
        $ldBlocks[] = $jsonLd;
    }
}
function render_ld($block) {
    if (is_array($block) && (isset($block['@type']) || isset($block['@context']))) {
        return json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    return '';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="<?= ASSETS ?>/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= ASSETS ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= ASSETS ?>/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= ASSETS ?>/apple-touch-icon.png">
    <link rel="manifest" href="<?= ASSETS ?>/site.webmanifest">
    <link rel="stylesheet" href="<?= ASSETS ?>/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <script type="application/ld+json">
<?php foreach ($ldBlocks as $b): ?>
<?= render_ld($b) ?>
<?php endforeach; ?>
    </script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6709647601744424"
     crossorigin="anonymous"></script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<a class="skip-link" href="#content">Skip to content</a>

<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= SITE_URL ?>/">
            <img src="<?= logo_url() ?>" alt="<?= e(SITE_NAME) ?> logo" width="170" height="48">
        </a>
        <button class="nav-toggle" aria-label="Toggle menu" aria-expanded="false">☰</button>
        <nav class="main-nav" aria-label="Primary">
            <ul>
                <li class="has-dropdown">
                    <a href="<?= SITE_URL ?>/blog"><svg class="nav-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Blog</a>
                    <ul class="dropdown">
                        <?php foreach (get_categories() as $c): ?>
                        <li><a href="<?= SITE_URL ?>/category/<?= e($c['slug']) ?>"><?= e($c['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li><a href="<?= SITE_URL ?>/category/side-hustles"><svg class="nav-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>Side Hustles</a></li>
                <li><a href="<?= SITE_URL ?>/p/about"><svg class="nav-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>About</a></li>
                <li><a href="<?= SITE_URL ?>/p/contact"><svg class="nav-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>Contact</a></li>
                <li><a class="btn-cta" href="<?= SITE_URL ?>/p/sign-up-offers"><svg class="nav-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5C11 3 12 8 12 8s1-5 4.5-5a2.5 2.5 0 0 1 0 5"/></svg>Sign Up Offers</a></li>
            </ul>
        </nav>
    </div>
</header>

<main id="content">
