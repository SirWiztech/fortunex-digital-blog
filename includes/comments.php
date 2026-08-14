<?php
/**
 * Fortunexdigital — comment system
 */
if (!function_exists('comments_section')) {
    function comments_section($post_id) {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("SELECT name, comment, created_at FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at ASC");
        $stmt->execute([$post_id]);
        $comments = $stmt->fetchAll();

        echo '<section class="comments" id="comments" aria-label="Comments">';
        echo '<h3>Comments (' . count($comments) . ')</h3>';

        if (!empty($comments)) {
            echo '<ul class="comment-list">';
            foreach ($comments as $c) {
                echo '<li><strong>' . e($c['name']) . '</strong> <span class="muted">' . format_date($c['created_at']) . '</span>';
                echo '<p>' . nl2br(e($c['comment'])) . '</p></li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="muted">Be the first to comment!</p>';
        }

        echo '<form class="comment-form" method="post" action="' . SITE_URL . '/post.php?slug=' . e($_GET['slug'] ?? '') . '#comments">';
        echo '<h4>Leave a comment</h4>';
        echo '<input type="hidden" name="post_id" value="' . e($post_id) . '">';
        echo '<label>Name <input type="text" name="name" required maxlength="80"></label>';
        echo '<label>Email <input type="email" name="email" required maxlength="120"></label>';
        echo '<label>Comment <textarea name="comment" rows="4" required></textarea></label>';
        echo '<button type="submit" name="submit_comment" class="btn-cta">Post Comment</button>';
        echo '</form>';
        echo '</section>';
    }
}

if (isset($_POST['submit_comment'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $post_id = (int)($_POST['post_id'] ?? 0);
    if ($name && $email && $comment && $post_id) {
        try {
            $pdo = DB::connect();
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, name, email, comment, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$post_id, $name, $email, $comment]);
            $_SESSION['comment_notice'] = 'Thanks! Your comment is awaiting moderation.';
        } catch (Exception $e) {
            $_SESSION['comment_notice'] = 'Sorry, there was an error posting your comment.';
        }
    }
    $slug = $_GET['slug'] ?? '';
    header('Location: ' . SITE_URL . '/post.php?slug=' . urlencode($slug) . '#comments');
    exit;
}
