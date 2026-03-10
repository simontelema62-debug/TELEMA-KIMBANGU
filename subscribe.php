<?php

require_once __DIR__ . '/includes/auth.php';

require_post_request();
verify_csrf();

$email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
if ($email) {
    $stmt = db()->prepare('INSERT IGNORE INTO subscribers (email) VALUES (:email)');
    $stmt->execute(['email' => $email]);
}

header('Location: ' . BASE_URL . '/index.php');
exit;
