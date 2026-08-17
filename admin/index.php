<?php
/**
 * Fortunexdigital — Admin dashboard (post CRUD + traffic stats)
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';
require_admin();

$pdo = DB::connect();
$action = $_GET['action'] ?? 'list';
$message = '';

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([(int)$_GET['id']]);
    $action = 'list';
    $message = 'Post deleted.';
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_post'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']) ?: slugify($title);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $author_id = (int)($_POST['author_id'] ?? 1);
    $excerpt = trim($_POST['excerpt']);
    $content = $_POST['content'];
    $featured_image = trim($_POST['featured_image'] ?? '');
    $status = $_POST['status'] ?? 'published';
    $meta_title = trim($_POST['meta_title']);
    $meta_description = trim($_POST['meta_description']);
    $tags = trim($_POST['tags'] ?? '');

    if ($id > 0) {
        $pdo->prepare("UPDATE posts SET title=?, slug=?, category_id=?, author_id=?, excerpt=?, content=?, featured_image=?, status=?, meta_title=?, meta_description=?, updated_at=NOW() WHERE id=?")
            ->execute([$title, $slug, $category_id, $author_id, $excerpt, $content, $featured_image, $status, $meta_title, $meta_description, $id]);
    } else {
        $pdo->prepare("INSERT INTO posts (title, slug, category_id, author_id, excerpt, content, featured_image, status, meta_title, meta_description, published_at, updated_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),NOW())")
            ->execute([$title, $slug, $category_id, $author_id, $excerpt, $content, $featured_image, $status, $meta_title, $meta_description]);
        $id = $pdo->lastInsertId();
    }

    // Tags
    $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?")->execute([$id]);
    if ($tags !== '') {
        foreach (array_filter(array_map('trim', explode(',', $tags))) as $tname) {
            $tslug = slugify($tname);
            $chk = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
            $chk->execute([$tslug]);
            $tid = $chk->fetchColumn();
            if (!$tid) {
                $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)")->execute([$tname, $tslug]);
                $tid = $pdo->lastInsertId();
            }
            $pdo->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)")->execute([$id, $tid]);
        }
    }
    $message = 'Post saved.';

    if (isset($_POST['continue_edit'])) {
        header('Location: ' . SITE_URL . '/admin/index.php?action=edit&id=' . $id);
        exit;
    }
    $action = 'list';
}

// Form data
$post = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $post = $pdo->prepare("SELECT * FROM posts WHERE id = ?")->fetch();
    if ($post) {
        $ptags = $pdo->prepare("SELECT t.name FROM tags t JOIN post_tags pt ON pt.tag_id=t.id WHERE pt.post_id=?");
        $ptags->execute([$post['id']]);
        $post['tags'] = implode(', ', array_column($ptags->fetchAll(), 'name'));
    }
}
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$authors = $pdo->query("SELECT * FROM authors ORDER BY name")->fetchAll();
$posts = $pdo->query("SELECT p.id, p.title, p.status, p.published_at, c.name AS category FROM posts p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.published_at DESC")->fetchAll();

// --- Traffic stats ---
$traffic_action = $_GET['traffic_action'] ?? 'overview';
$traffic_period = $_GET['period'] ?? 'all';

// Older databases may predate the traffic-stats feature and lack the
// `visits` table. Without it, every query below throws and the whole
// dashboard returns a 500. Create the table if missing so live installs
// that haven't re-run install.php still work.
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(45),
        user_agent TEXT,
        referer TEXT,
        page_url TEXT,
        visited_at DATETIME
    )");
} catch (PDOException $e) {
    // Degrade gracefully below instead of crashing the page.
}

// Reset visit statistics (POST only, so crawlers/prefetch can't wipe data)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $traffic_action === 'reset') {
    try {
        $pdo->exec("DELETE FROM visits");
        $message = 'All visit statistics have been reset to zero.';
    } catch (PDOException $e) {
        $message = 'Could not reset visit statistics: ' . $e->getMessage();
    }
    $traffic_action = 'overview';
}

$stats_error = '';
$total_visits = $unique_visitors = $today_visits = $this_week = $this_month = 0;
$top_pages = $referrers = $recent = [];
try {
    $total_visits = $pdo->query("SELECT COUNT(*) as cnt FROM visits")->fetch()['cnt'];
    $unique_visitors = $pdo->query("SELECT COUNT(DISTINCT ip) as cnt FROM visits")->fetch()['cnt'];
    $today_visits = $pdo->query("SELECT COUNT(*) as cnt FROM visits WHERE visited_at >= CURDATE()")->fetch()['cnt'];
    $this_week = $pdo->query("SELECT COUNT(*) as cnt FROM visits WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch()['cnt'];
    $this_month = $pdo->query("SELECT COUNT(*) as cnt FROM visits WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch()['cnt'];

    // Top pages
    $top_pages = $pdo->query("SELECT page_url, COUNT(*) as visits FROM visits GROUP BY page_url ORDER BY visits DESC LIMIT 10")->fetchAll();

    // Referrers
    $referrers = $pdo->query("SELECT referer, COUNT(*) as cnt FROM visits WHERE referer != '' AND referer IS NOT NULL GROUP BY referer ORDER BY cnt DESC LIMIT 10")->fetchAll();

    // Recent visits
    $recent = $pdo->query("SELECT ip, page_url, visited_at FROM visits ORDER BY visited_at DESC LIMIT 20")->fetchAll();
} catch (PDOException $e) {
    $stats_error = 'Traffic statistics are unavailable: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — <?= SITE_NAME ?></title>
<link rel="stylesheet" href="<?= ASSETS ?>/css/style.css">
<style>
  .admin-wrap{max-width:960px;margin:0 auto;padding:30px 20px}
  .admin-table{width:100%;border-collapse:collapse}
  .admin-table th,.admin-table td{border:1px solid var(--border);padding:8px 10px;text-align:left}
  .admin-form label{display:block;font-weight:600;margin:12px 0 4px}
  .admin-form input,.admin-form textarea,.admin-form select{width:100%;padding:10px;border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:15px}
  .admin-form textarea{min-height:260px}
  .pill{display:inline-block;padding:2px 10px;border-radius:999px;font-size:12px;background:var(--bg-soft)}
  .stat-box{background:var(--bg-soft);border-radius:var(--radius);padding:20px;margin:10px 0}
  .stat-box h3{margin:0 0 12px;font-size:24px;color:var(--primary)}
  .stat-box p{margin:0;color:var(--muted)}
  .traffic-filter{margin:20px 0}
  .traffic-filter select,.traffic-filter button{padding:8px 14px;font-size:14px}
  .recent-visit{font-size:12px;color:var(--muted);background:#fafafa;padding:8px 12px;margin:4px 0;border-radius:4px}
</style>
</head>
<body>
<header class="site-header"><div class="container header-inner">
  <a class="brand" href="<?= SITE_URL ?>/"><img src="<?= logo_url() ?>" alt="logo" height="36"></a>
  <nav><a href="<?= SITE_URL ?>/admin/logout.php">Log out</a> · <a href="<?= SITE_URL ?>/">View site</a></nav>
</div></header>

<div class="admin-wrap">

  <?php if ($message): ?><p class="pill" style="background:#e8f5e9;color:#1b5e20"><?= e($message) ?></p><?php endif; ?>
  <?php if ($stats_error): ?><p class="pill" style="background:#fff3e0;color:#e65100"><?= e($stats_error) ?></p><?php endif; ?>

  <!-- Traffic overview cards -->
  <div class="traffic-filter">
    <label>Period: </label>
    <select name="period" onchange="window.location.search='period='+this.value">
      <option value="all" <?= $traffic_period === 'all' ? 'selected' : '' ?>>All time</option>
      <option value="day" <?= $traffic_period === 'day' ? 'selected' : '' ?>>Today</option>
      <option value="week" <?= $traffic_period === 'week' ? 'selected' : '' ?>>This week</option>
      <option value="month" <?= $traffic_period === 'month' ? 'selected' : '' ?>>This month</option>
    </select>
    <form method="post" action="<?= SITE_URL ?>/admin/index.php?traffic_action=reset" style="display:inline;margin-left:8px" onsubmit="return confirm('Reset ALL visit statistics to zero? This cannot be undone.');">
      <button class="btn-outline" type="submit" style="font-size:12px;padding:6px 12px">Reset Stats</button>
    </form>
  </div>

  <div class="stat-box">
    <h3><?= $total_visits ?></h3>
    <p>Total Visits</p>
  </div>
  <div class="stat-box">
    <h3><?= $unique_visitors ?></h3>
    <p>Unique Visitors</p>
  </div>
  <div class="stat-box">
    <h3><?= $today_visits ?></h3>
    <p>Today</p>
  </div>
  <div class="stat-box">
    <h3><?= $this_week ?></h3>
    <p>This Week</p>
  </div>
  <div class="stat-box">
    <h3><?= $this_month ?></h3>
    <p>This Month</p>
  </div>

  <!-- Tabs -->
  <div style="display:flex;gap:12px;margin:30px 0;flex-wrap:wrap">
    <a class="btn-cta" href="<?= SITE_URL ?>/admin/index.php?action=list">Posts</a>
    <a class="btn-outline" href="<?= SITE_URL ?>/admin/index.php?traffic_action=pages">Top Pages</a>
    <a class="btn-outline" href="<?= SITE_URL ?>/admin/index.php?traffic_action=referrers">Referrers</a>
    <a class="btn-outline" href="<?= SITE_URL ?>/admin/index.php?traffic_action=recent">Recent Visits</a>
  </div>

  <?php if ($traffic_action === 'pages'): ?>
    <h2>Top Pages</h2>
    <table class="admin-table">
      <tr><th>Page</th><th>Visits</th></tr>
      <?php foreach ($top_pages as $p): ?>
        <tr><td><?= e($p['page_url']) ?></td><td><?= $p['visits'] ?></td></tr>
      <?php endforeach; ?>
    </table>
  <?php elseif ($traffic_action === 'referrers'): ?>
    <h2>Top Referrers</h2>
    <table class="admin-table">
      <tr><th>Referrer</th><th>Clicks</th></tr>
      <?php foreach ($referrers as $r): ?>
        <tr><td><?= e($r['referer'] ?: '(direct)') ?></td><td><?= $r['cnt'] ?></td></tr>
      <?php endforeach; ?>
    </table>
  <?php elseif ($traffic_action === 'recent'): ?>
    <h2>Recent Visits</h2>
    <div style="max-height:400px;overflow:auto">
      <?php foreach ($recent as $v): ?>
        <div class="recent-visit"><?= e($v['ip']) ?> · <?= e($v['page_url']) ?> · <?= format_date($v['visited_at']) ?></div>
      <?php endforeach; ?>
    </div>
  <?php else: /* overview */ ?>
    <!-- Already shown above -->
  <?php endif; ?>

  <!-- Posts table -->
  <?php if ($action === 'list'): ?>
    <div style="display:flex;justify-content:space-between;align-items:center">
      <h1>Posts</h1>
      <a class="btn-cta" href="<?= SITE_URL ?>/admin/index.php?action=new">+ New Post</a>
    </div>
    <table class="admin-table">
      <tr><th>Title</th><th>Category</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td><?= e($p['title']) ?></td>
        <td><?= e($p['category']) ?></td>
        <td><?= e($p['status']) ?></td>
        <td><?= format_date($p['published_at']) ?></td>
        <td>
          <a href="<?= SITE_URL ?>/admin/index.php?action=edit&id=<?= $p['id'] ?>">Edit</a> ·
          <a href="<?= SITE_URL ?>/admin/index.php?action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Delete this post?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php else: /* new / edit */ ?>
    <h1><?= $action === 'edit' ? 'Edit Post' : 'New Post' ?></h1>
    <form class="admin-form" method="post" action="<?= SITE_URL ?>/admin/index.php?action=save">
      <input type="hidden" name="id" value="<?= $post['id'] ?? 0 ?>">
      <label>Title</label>
      <input type="text" name="title" value="<?= e($post['title'] ?? '') ?>" required>
      <label>Slug (auto if blank)</label>
      <input type="text" name="slug" value="<?= e($post['slug'] ?? '') ?>">
      <label>Category</label>
      <select name="category_id">
        <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (($post['category_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
      </select>
      <label>Author</label>
      <select name="author_id">
        <?php foreach ($authors as $a): ?><option value="<?= $a['id'] ?>" <?= (($post['author_id'] ?? 1) == $a['id']) ? 'selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
      </select>
      <label>Excerpt</label>
      <textarea name="excerpt" style="min-height:80px"><?= e($post['excerpt'] ?? '') ?></textarea>
      <label>Featured Image URL</label>
      <input type="url" name="featured_image" value="<?= e($post['featured_image'] ?? '') ?>" placeholder="https://...">
      <label>Content (HTML allowed)</label>
      <textarea name="content"><?= e($post['content'] ?? '') ?></textarea>
      <label>Tags (comma-separated)</label>
      <input type="text" name="tags" value="<?= e($post['tags'] ?? '') ?>">
      <label>Status</label>
      <select name="status">
        <option value="published" <?= (($post['status'] ?? 'published')=='published')?'selected':'' ?>>Published</option>
        <option value="draft" <?= (($post['status'] ?? '')=='draft')?'selected':'' ?>>Draft</option>
      </select>
      <label>Meta Title (SEO)</label>
      <input type="text" name="meta_title" value="<?= e($post['meta_title'] ?? '') ?>">
      <label>Meta Description (SEO)</label>
      <input type="text" name="meta_description" value="<?= e($post['meta_description'] ?? '') ?>">
      <p style="margin-top:16px">
        <button class="btn-cta" type="submit" name="save_post">Save Post</button>
        <button class="btn-outline" type="submit" name="continue_edit" value="1" style="margin-left:8px">Save & Keep Editing</button>
        <a href="<?= SITE_URL ?>/admin/index.php" style="margin-left:12px">Cancel</a>
      </p>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
