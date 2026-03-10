<?php

define('APP_NAME', 'TELEMA KIMBANGU (TKS)');
define('APP_SHORT', 'TKS');
define('BASE_URL', '/TKS');
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('CV_DIR', UPLOAD_DIR . '/cv');
define('MEDIA_DIR', UPLOAD_DIR . '/media');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);
define('ENFORCE_HTTPS', false);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'tks_site');
define('DB_USER', 'root');
define('DB_PASS', '');

if (ENFORCE_HTTPS && empty($_SERVER['HTTPS'])) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', $isHttps ? '1' : '0');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; media-src 'self'; font-src 'self' https://fonts.gstatic.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'");
