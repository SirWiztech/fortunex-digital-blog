<?php
/**
 * Fortunexdigital — site footer (semantic <footer>)
 */
global $canonicalUrl;
$catLinks = '';
foreach (get_categories() as $c) {
    $catLinks .= '<li><a href="' . SITE_URL . '/category/' . e($c['slug']) . '">' . e($c['name']) . '</a></li>';
}
$year = date('Y');
?>
    </main><!-- /#content -->

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-col footer-brand">
                <img src="<?= logo_url() ?>" alt="<?= e(SITE_NAME) ?> logo" width="170" height="48">
                <p class="tagline"><?= e(SITE_TAGLINE) ?></p>
            </div>
            <div class="footer-col">
                <h4>Learn</h4>
                <ul><?= $catLinks ?></ul>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/">Home</a></li>
                    <li><a href="<?= SITE_URL ?>/p/about">About</a></li>
                    <li><a href="<?= SITE_URL ?>/p/contact">Contact</a></li>
                    <li><a href="<?= SITE_URL ?>/blog">Blog</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Follow Us!</h4>
                <ul class="social">
                    <li><a href="https://www.facebook.com/fortunexdigital" rel="noopener">Facebook</a></li>
                    <li><a href="https://www.youtube.com/@fortunexdigital" rel="noopener">YouTube</a></li>
                    <li><a href="https://www.pinterest.com/fortunexdigital" rel="noopener">Pinterest</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>All Rights Reserved. Copyright © <?= $year ?> <strong><?= e(SITE_NAME) ?></strong></p>
            <ul class="legal">
                <li><a href="<?= SITE_URL ?>/p/terms">Terms</a></li>
                <li><a href="<?= SITE_URL ?>/p/disclaimer">Disclaimer</a></li>
                <li><a href="<?= SITE_URL ?>/p/privacy-policy">Privacy Policy</a></li>
                <li><a href="<?= SITE_URL ?>/p/cookie-policy">Cookie Policy</a></li>
            </ul>
        </div>
    </footer>

    <div id="cookie-banner" class="cookie-banner" hidden>
        <p><?= e(COOKIE_TEXT) ?> <a href="<?= SITE_URL ?>/p/cookie-policy">Learn more</a>.</p>
        <button id="cookie-accept">Got it!</button>
    </div>

    <script src="<?= ASSETS ?>/js/main.js" defer></script>
</body>
</html>
