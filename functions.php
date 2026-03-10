<?php

require_once __DIR__ . '/db.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function get_profile(): array
{
    $stmt = db()->query('SELECT * FROM profile WHERE id = 1 LIMIT 1');
    $row = $stmt->fetch();
    return $row ?: [
        'name' => 'TELEMA KIMBANGU (TKS)',
        'status_message' => 'Bienvenue sur mon espace digital.',
        'bio' => 'Biographie non renseignee.',
        'profile_photo' => '',
        'profile_photo_2' => '',
        'cv_file' => '',
        'birth_date' => '',
        'birth_place' => '',
        'country' => '',
        'province' => '',
        'territory' => '',
        'sector' => '',
        'grouping' => '',
        'village' => '',
        'father_name' => '',
        'mother_name' => '',
        'primary_education' => '',
        'secondary_education' => '',
        'university_education' => '',
        'life_history' => '',
        'facebook_url' => '',
        'linkedin_url' => '',
        'youtube_url' => '',
        'website_url' => '',
        'phone_1' => '+243 997 972 669',
        'phone_2' => '+243 893 473 370',
        'email' => 'Rufilsdiakugilette@gmail.com',
        'whatsapp' => '243997972669',
    ];
}

function stat_value(string $key, int $default = 0): int
{
    $stmt = db()->prepare('SELECT stat_value FROM site_stats WHERE stat_key = :k LIMIT 1');
    $stmt->execute(['k' => $key]);
    $row = $stmt->fetch();
    return $row ? (int) $row['stat_value'] : $default;
}

function increment_visitors_once(): void
{
    if (!empty($_SESSION['visited_once'])) {
        return;
    }
    $_SESSION['visited_once'] = true;

    $pdo = db();
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT stat_value FROM site_stats WHERE stat_key = :k FOR UPDATE');
        $stmt->execute(['k' => 'visitors']);
        $row = $stmt->fetch();
        if ($row) {
            $newValue = (int) $row['stat_value'] + 1;
            $upd = $pdo->prepare('UPDATE site_stats SET stat_value = :v WHERE stat_key = :k');
            $upd->execute(['v' => $newValue, 'k' => 'visitors']);
        } else {
            $ins = $pdo->prepare('INSERT INTO site_stats (stat_key, stat_value) VALUES (:k, 1)');
            $ins->execute(['k' => 'visitors']);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}

function valid_upload(array $file, array $allowedExt): bool
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return false;
    }
    if (($file['size'] ?? 0) > MAX_UPLOAD_SIZE) {
        return false;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return false;
    }

    if (!is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
        return false;
    }

    if (function_exists('finfo_open')) {
        $allowedMimeByExt = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
            'pdf' => ['application/pdf'],
            'mp4' => ['video/mp4'],
            'webm' => ['video/webm'],
            'mp3' => ['audio/mpeg'],
            'wav' => ['audio/wav', 'audio/x-wav'],
            'ogg' => ['audio/ogg'],
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? (string) finfo_file($finfo, $file['tmp_name']) : '';
        if ($finfo) {
            finfo_close($finfo);
        }
        $allowedMimes = $allowedMimeByExt[$ext] ?? [];
        if ($mime === '' || ($allowedMimes && !in_array($mime, $allowedMimes, true))) {
            return false;
        }
    }

    return true;
}

function unique_filename(string $original): string
{
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    return bin2hex(random_bytes(8)) . '_' . time() . ($ext ? '.' . $ext : '');
}

function ensure_upload_directories(): void
{
    foreach ([UPLOAD_DIR, CV_DIR, MEDIA_DIR] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

function normalize_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }

    $validated = filter_var($url, FILTER_VALIDATE_URL);
    return $validated ? $validated : '';
}
