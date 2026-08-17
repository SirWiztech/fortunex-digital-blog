<?php
/**
 * Fortunexdigital — helper functions
 */
require_once __DIR__ . '/db.php';

if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('slugify')) {
    function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        $text = preg_replace('~[^a-z0-9-]+~', '', $text);
        return $text ?: 'post';
    }
}

if (!function_exists('excerpt')) {
    function excerpt($html, $words = 28) {
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', trim($text));
        $parts = explode(' ', $text);
        if (count($parts) <= $words) return $text;
        return implode(' ', array_slice($parts, 0, $words)) . '…';
    }
}

if (!function_exists('format_date')) {
    function format_date($mysql_date) {
        return date('F j, Y', strtotime($mysql_date));
    }
}

if (!function_exists('get_categories')) {
    function get_categories() {
        $pdo = DB::connect();
        return $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }
}

if (!function_exists('category_image')) {
    function category_image($cat) {
        $img = $cat['image'] ?? '';
        if ($img) {
            // Accept a full URL or a root-relative path like /assets/img/...
            if (strpos($img, 'http') !== 0 && strpos($img, '/') === 0) {
                $img = SITE_URL . $img;
            }
            return $img;
        }
        // Fallback: file named by slug, e.g. categories_side-hustles.jpg
        return ASSETS . '/img/categories_' . $cat['slug'] . '.jpg';
    }
}

if (!function_exists('get_post')) {
    function get_post($slug) {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug, a.name AS author_name, a.bio AS author_bio, a.avatar AS author_avatar
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN authors a ON p.author_id = a.id
            WHERE p.slug = ? AND p.status = 'published'
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
}

if (!function_exists('get_related')) {
    function get_related($post, $limit = 3) {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("
            SELECT p.id, p.title, p.slug, p.featured_image, p.excerpt, c.slug AS category_slug
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
            ORDER BY p.published_at DESC LIMIT ?
        ");
        $stmt->execute([$post['category_id'], $post['id'], $limit]);
        $rows = $stmt->fetchAll();
        if (count($rows) < $limit) {
            $stmt = $pdo->prepare("
                SELECT p.id, p.title, p.slug, p.featured_image, p.excerpt, c.slug AS category_slug
                FROM posts p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id != ? AND p.status = 'published'
                ORDER BY p.published_at DESC LIMIT ?
            ");
            $stmt->execute([$post['id'], $limit]);
            $rows = $stmt->fetchAll();
        }
        return $rows;
    }
}

if (!function_exists('post_thumb')) {
    function post_thumb($post, $fallback = '') {
        $img = $post['featured_image'] ?? '';
        if ($img) {
            return '<img src="' . e($img) . '" alt="' . e($post['title'] ?? $fallback) . '" loading="lazy" width="800" height="450">';
        }
        return e($fallback ?: ($post['category_name'] ?? 'Read more'));
    }
}

if (!function_exists('get_tags_for_post')) {
    function get_tags_for_post($post_id) {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("
            SELECT t.name, t.slug FROM tags t
            JOIN post_tags pt ON pt.tag_id = t.id
            WHERE pt.post_id = ? ORDER BY t.name ASC
        ");
        $stmt->execute([$post_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('pagination')) {
    function pagination($total, $per_page, $current, $base_url) {
        $pages = ceil($total / $per_page);
        if ($pages <= 1) return '';
        $html = '<nav class="pagination" aria-label="Pagination"><ul>';
        if ($current > 1) {
            $html .= '<li><a href="' . $base_url . '?page=' . ($current - 1) . '">« Prev</a></li>';
        }
        for ($i = 1; $i <= $pages; $i++) {
            $active = $i == $current ? ' class="active"' : '';
            $html .= '<li' . $active . '><a href="' . $base_url . '?page=' . $i . '">' . $i . '</a></li>';
        }
        if ($current < $pages) {
            $html .= '<li><a href="' . $base_url . '?page=' . ($current + 1) . '">Next »</a></li>';
        }
        $html .= '</ul></nav>';
        return $html;
    }
}

if (!function_exists('breadcrumb_jsonld')) {
    function breadcrumb_jsonld($trail) {
        $list = [];
        $i = 1;
        foreach ($trail as $name => $url) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i++,
                'name' => $name,
                'item' => $url
            ];
        }
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list
        ];
    }
}

if (!function_exists('logo_url')) {
    function logo_url() {
        $dir = __DIR__ . '/../assets/img/';
        $candidates = ['Fortunex-Logo', 'fortunex-logo', 'FortunexLogo', 'fortunexlogo', 'logo'];
        $exts = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif'];
        foreach ($candidates as $base) {
            foreach ($exts as $ext) {
                $path = $dir . $base . '.' . $ext;
                if (is_file($path)) {
                    return ASSETS . '/img/' . basename($path);
                }
            }
        }
        return ASSETS . '/img/logo.svg';
    }
}

if (!function_exists('organization_jsonld')) {
    function organization_jsonld() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => SITE_NAME,
            'url' => SITE_URL,
            'logo' => logo_url(),
            'description' => SITE_TAGLINE,
            'sameAs' => [
                'https://www.facebook.com/fortunexdigital',
                'https://www.youtube.com/@fortunexdigital',
                'https://www.pinterest.com/fortunexdigital'
            ]
        ];
    }
}

if (!function_exists('track_visit')) {
    function track_visit() {
        if (!function_exists('DB')) require_once __DIR__ . '/db.php';
        $pdo = DB::connect();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $page = $_SERVER['REQUEST_URI'] ?? '/';
        try {
            $pdo->prepare("INSERT INTO visits (ip, user_agent, referer, page_url, visited_at) VALUES (?, ?, ?, ?, NOW())")->execute([$ip, $ua, $ref, $page]);
        } catch (Exception $e) {
            // silently fail if DB not available
        }
    }
}
