<?php

require_once __DIR__ . '/includes/auth.php';

require_post_request();
verify_csrf();

$postId = (int) ($_POST['post_id'] ?? 0);
if ($postId > 0) {
    $key = 'liked_' . $postId;
    if (empty($_SESSION[$key])) {
        $_SESSION[$key] = true;
        db()->prepare('UPDATE posts SET likes_count = likes_count + 1 WHERE id = :id')->execute(['id' => $postId]);
    }
}

header('Location: ' . BASE_URL . '/index.php#actus');
exit;
