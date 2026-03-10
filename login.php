<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!login($username, $password)) {
        $error = 'Identifiants invalides.';
    } else {
        header('Location: ' . BASE_URL . '/admin.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion Admin - TELEMA KIMBANGU (TKS)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(BASE_URL) ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-soft">
  <div class="container py-5">
    <div class="mx-auto card-rdn p-4" style="max-width: 420px;">
      <h1 class="h4 fw-bold">Connexion administrateur</h1>
      <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="mb-3"><label class="form-label">Nom d'utilisateur</label><input class="form-control" name="username" required></div>
        <div class="mb-3"><label class="form-label">Mot de passe</label><input class="form-control" type="password" name="password" required></div>
        <button class="btn btn-rdn w-100" type="submit">Se connecter</button>
      </form>
      <a class="d-block text-center mt-3" href="<?= e(BASE_URL) ?>/index.php">Retour au site</a>
    </div>
  </div>
</body>
</html>
