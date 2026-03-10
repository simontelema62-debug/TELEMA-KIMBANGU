<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_admin();
ensure_upload_directories();
$message = '';

function delete_media_file(string $filename): void
{
    $filename = trim($filename);
    if ($filename === '') {
        return;
    }
    $path = MEDIA_DIR . '/' . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $currentProfile = get_profile();
        $uploadedPhoto1 = false;
        $uploadedPhoto2 = false;
        $data = [
            'name' => trim((string) $_POST['name']),
            'status_message' => trim((string) $_POST['status_message']),
            'bio' => trim((string) $_POST['bio']),
            'birth_date' => trim((string) ($_POST['birth_date'] ?? '')),
            'birth_place' => trim((string) ($_POST['birth_place'] ?? '')),
            'country' => trim((string) ($_POST['country'] ?? '')),
            'province' => trim((string) ($_POST['province'] ?? '')),
            'territory' => trim((string) ($_POST['territory'] ?? '')),
            'sector' => trim((string) ($_POST['sector'] ?? '')),
            'grouping' => trim((string) ($_POST['grouping'] ?? '')),
            'village' => trim((string) ($_POST['village'] ?? '')),
            'father_name' => trim((string) ($_POST['father_name'] ?? '')),
            'mother_name' => trim((string) ($_POST['mother_name'] ?? '')),
            'primary_education' => trim((string) ($_POST['primary_education'] ?? '')),
            'secondary_education' => trim((string) ($_POST['secondary_education'] ?? '')),
            'university_education' => trim((string) ($_POST['university_education'] ?? '')),
            'life_history' => trim((string) ($_POST['life_history'] ?? '')),
            'facebook_url' => normalize_url((string) $_POST['facebook_url']),
            'linkedin_url' => normalize_url((string) $_POST['linkedin_url']),
            'youtube_url' => normalize_url((string) $_POST['youtube_url']),
            'website_url' => normalize_url((string) $_POST['website_url']),
            'phone_1' => trim((string) $_POST['phone_1']),
            'phone_2' => trim((string) $_POST['phone_2']),
            'email' => trim((string) $_POST['email']),
            'whatsapp' => trim((string) $_POST['whatsapp']),
        ];

        $sql = 'UPDATE profile SET name=:name, status_message=:status_message, bio=:bio, birth_date=:birth_date, birth_place=:birth_place, country=:country, province=:province, territory=:territory, sector=:sector, grouping=:grouping, village=:village, father_name=:father_name, mother_name=:mother_name, primary_education=:primary_education, secondary_education=:secondary_education, university_education=:university_education, life_history=:life_history, facebook_url=:facebook_url, linkedin_url=:linkedin_url, youtube_url=:youtube_url, website_url=:website_url, phone_1=:phone_1, phone_2=:phone_2, email=:email, whatsapp=:whatsapp WHERE id=1';
        db()->prepare($sql)->execute($data);

        if (valid_upload($_FILES['profile_photo'] ?? [], ['jpg', 'jpeg', 'png', 'webp'])) {
            $filename = unique_filename($_FILES['profile_photo']['name']);
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], MEDIA_DIR . '/' . $filename);
            delete_media_file((string) ($currentProfile['profile_photo'] ?? ''));
            db()->prepare('UPDATE profile SET profile_photo=:p WHERE id=1')->execute(['p' => $filename]);
            $uploadedPhoto1 = true;
        }
        if (valid_upload($_FILES['profile_photo_2'] ?? [], ['jpg', 'jpeg', 'png', 'webp'])) {
            $filename = unique_filename($_FILES['profile_photo_2']['name']);
            move_uploaded_file($_FILES['profile_photo_2']['tmp_name'], MEDIA_DIR . '/' . $filename);
            delete_media_file((string) ($currentProfile['profile_photo_2'] ?? ''));
            db()->prepare('UPDATE profile SET profile_photo_2=:p WHERE id=1')->execute(['p' => $filename]);
            $uploadedPhoto2 = true;
        }

        if (!$uploadedPhoto1 && !empty($_POST['remove_photo_1'])) {
            delete_media_file((string) ($currentProfile['profile_photo'] ?? ''));
            db()->prepare('UPDATE profile SET profile_photo=\'\' WHERE id=1')->execute();
        }
        if (!$uploadedPhoto2 && !empty($_POST['remove_photo_2'])) {
            delete_media_file((string) ($currentProfile['profile_photo_2'] ?? ''));
            db()->prepare('UPDATE profile SET profile_photo_2=\'\' WHERE id=1')->execute();
        }

        if (valid_upload($_FILES['cv_file'] ?? [], ['pdf'])) {
            $filename = unique_filename($_FILES['cv_file']['name']);
            move_uploaded_file($_FILES['cv_file']['tmp_name'], CV_DIR . '/' . $filename);
            db()->prepare('UPDATE profile SET cv_file=:c WHERE id=1')->execute(['c' => $filename]);
        }

        $message = 'Profil mis a jour.';
    }

    if ($action === 'new_post') {
        $title = trim((string) $_POST['title']);
        $content = trim((string) $_POST['content']);
        if ($title !== '' && $content !== '') {
            db()->prepare('INSERT INTO posts (title, content) VALUES (:t,:c)')->execute(['t' => $title, 'c' => $content]);
            $message = 'Actualite ajoutee.';
        }
    }

    if ($action === 'delete_post') {
        db()->prepare('DELETE FROM posts WHERE id=:id')->execute(['id' => (int) $_POST['post_id']]);
        $message = 'Actualite supprimee.';
    }

    if ($action === 'new_portfolio') {
        $title = trim((string) $_POST['p_title']);
        $description = trim((string) $_POST['p_description']);
        $url = normalize_url((string) $_POST['p_url']);
        $image = '';
        if (valid_upload($_FILES['p_image'] ?? [], ['jpg', 'jpeg', 'png', 'webp'])) {
            $image = unique_filename($_FILES['p_image']['name']);
            move_uploaded_file($_FILES['p_image']['tmp_name'], MEDIA_DIR . '/' . $image);
        }

        if ($title !== '') {
            db()->prepare('INSERT INTO portfolio_items (title, description, project_url, image_path) VALUES (:t,:d,:u,:i)')->execute([
                't' => $title, 'd' => $description, 'u' => $url, 'i' => $image
            ]);
            $message = 'Projet portfolio ajoute.';
        }
    }

    if ($action === 'delete_portfolio') {
        db()->prepare('DELETE FROM portfolio_items WHERE id=:id')->execute(['id' => (int) $_POST['portfolio_id']]);
        $message = 'Projet supprime.';
    }

    if ($action === 'new_media') {
        $title = trim((string) $_POST['m_title']);
        $type = (string) $_POST['m_type'];
        $allowed = $type === 'video' ? ['mp4', 'webm'] : ($type === 'audio' ? ['mp3', 'wav', 'ogg'] : ['jpg', 'jpeg', 'png', 'webp']);

        if ($title !== '' && valid_upload($_FILES['m_file'] ?? [], $allowed)) {
            $file = unique_filename($_FILES['m_file']['name']);
            move_uploaded_file($_FILES['m_file']['tmp_name'], MEDIA_DIR . '/' . $file);
            db()->prepare('INSERT INTO media_items (title, media_type, file_path) VALUES (:t,:m,:f)')->execute([
                't' => $title, 'm' => $type, 'f' => $file
            ]);
            $message = 'Media ajoute.';
        }
    }

    if ($action === 'delete_media') {
        db()->prepare('DELETE FROM media_items WHERE id=:id')->execute(['id' => (int) $_POST['media_id']]);
        $message = 'Media supprime.';
    }

    if ($action === 'delete_subscriber') {
        db()->prepare('DELETE FROM subscribers WHERE id=:id')->execute(['id' => (int) $_POST['subscriber_id']]);
        $message = 'Abonne supprime.';
    }

    if ($action === 'change_password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        if ($new === '' || strlen($new) < 8) {
            $message = 'Le nouveau mot de passe doit contenir au moins 8 caracteres.';
        } elseif ($new !== $confirm) {
            $message = 'La confirmation ne correspond pas.';
        } else {
            $stmt = db()->prepare('SELECT password_hash FROM users WHERE id=:id LIMIT 1');
            $stmt->execute(['id' => (int) ($_SESSION['admin_id'] ?? 0)]);
            $row = $stmt->fetch();

            if (!$row || !password_verify($current, $row['password_hash'])) {
                $message = 'Mot de passe actuel incorrect.';
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                db()->prepare('UPDATE users SET password_hash=:p WHERE id=:id')->execute([
                    'p' => $hash,
                    'id' => (int) $_SESSION['admin_id'],
                ]);
                $message = 'Mot de passe mis a jour.';
            }
        }
    }

    if ($action === 'change_username') {
        $newUsername = trim((string) ($_POST['new_username'] ?? ''));
        if ($newUsername === '' || strlen($newUsername) < 3) {
            $message = 'Le nom utilisateur doit contenir au moins 3 caracteres.';
        } elseif (strlen($newUsername) > 50) {
            $message = 'Le nom utilisateur est trop long.';
        } else {
            $stmt = db()->prepare('SELECT COUNT(*) AS c FROM users WHERE username = :u AND id <> :id');
            $stmt->execute(['u' => $newUsername, 'id' => (int) $_SESSION['admin_id']]);
            $row = $stmt->fetch();
            if ($row && (int) $row['c'] > 0) {
                $message = 'Ce nom utilisateur est deja utilise.';
            } else {
                db()->prepare('UPDATE users SET username = :u WHERE id = :id')->execute([
                    'u' => $newUsername,
                    'id' => (int) $_SESSION['admin_id'],
                ]);
                $message = 'Nom utilisateur mis a jour.';
            }
        }
    }

    header('Location: ' . BASE_URL . '/admin.php?msg=' . urlencode($message));
    exit;
}

if (!empty($_GET['msg'])) {
    $message = (string) $_GET['msg'];
}

$profile = get_profile();
$posts = db()->query('SELECT id, title, created_at FROM posts ORDER BY created_at DESC')->fetchAll();
$portfolio = db()->query('SELECT id, title, created_at FROM portfolio_items ORDER BY created_at DESC')->fetchAll();
$media = db()->query('SELECT id, title, media_type, created_at FROM media_items ORDER BY created_at DESC')->fetchAll();
$subscribers = db()->query('SELECT id, email, created_at FROM subscribers ORDER BY created_at DESC LIMIT 20')->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administrateur - TELEMA KIMBANGU</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(BASE_URL) ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-soft">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3 fw-bold">TELEMA KIMBANGU</h1>
      <div>
        <a class="btn btn-outline-rdn" href="<?= e(BASE_URL) ?>/index.php">Voir le site</a>
        <a class="btn btn-rdn" href="<?= e(BASE_URL) ?>/logout.php">Deconnexion</a>
      </div>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <section class="card-rdn p-4 mb-4">
      <h2 class="h5 fw-bold">Profil, reseaux et CV</h2>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="update_profile">
        <div class="row g-3">
          <div class="col-md-6"><input class="form-control" name="name" placeholder="Nom" value="<?= e($profile['name']) ?>"></div>
          <div class="col-md-6"><input class="form-control" name="status_message" placeholder="Statut" value="<?= e($profile['status_message']) ?>"></div>
          <div class="col-12"><textarea class="form-control" name="bio" rows="4" placeholder="Bio"><?= e($profile['bio']) ?></textarea></div>
          <div class="col-md-4"><input class="form-control" name="birth_date" placeholder="Date de naissance" value="<?= e($profile['birth_date'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="birth_place" placeholder="Lieu de naissance" value="<?= e($profile['birth_place'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="country" placeholder="Pays" value="<?= e($profile['country'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="province" placeholder="Province" value="<?= e($profile['province'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="territory" placeholder="Territoire" value="<?= e($profile['territory'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="sector" placeholder="Secteur" value="<?= e($profile['sector'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="grouping" placeholder="Groupement" value="<?= e($profile['grouping'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="village" placeholder="Village" value="<?= e($profile['village'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="father_name" placeholder="Nom du pere" value="<?= e($profile['father_name'] ?? '') ?>"></div>
          <div class="col-md-4"><input class="form-control" name="mother_name" placeholder="Nom de la mere" value="<?= e($profile['mother_name'] ?? '') ?>"></div>
          <div class="col-md-4"><textarea class="form-control" name="primary_education" rows="2" placeholder="Parcours primaire"><?= e($profile['primary_education'] ?? '') ?></textarea></div>
          <div class="col-md-4"><textarea class="form-control" name="secondary_education" rows="2" placeholder="Parcours secondaire"><?= e($profile['secondary_education'] ?? '') ?></textarea></div>
          <div class="col-12"><textarea class="form-control" name="university_education" rows="2" placeholder="Parcours universitaire"><?= e($profile['university_education'] ?? '') ?></textarea></div>
          <div class="col-12"><textarea class="form-control" name="life_history" rows="4" placeholder="Bref historique (votre histoire)"><?= e($profile['life_history'] ?? '') ?></textarea></div>
          <div class="col-md-6"><input class="form-control" name="email" placeholder="Email" value="<?= e($profile['email']) ?>"></div>
          <div class="col-md-3"><input class="form-control" name="phone_1" placeholder="Telephone 1" value="<?= e($profile['phone_1']) ?>"></div>
          <div class="col-md-3"><input class="form-control" name="phone_2" placeholder="Telephone 2" value="<?= e($profile['phone_2']) ?>"></div>
          <div class="col-md-4"><input class="form-control" name="whatsapp" placeholder="WhatsApp" value="<?= e($profile['whatsapp']) ?>"></div>
          <div class="col-md-4"><input class="form-control" name="facebook_url" placeholder="URL Facebook" value="<?= e($profile['facebook_url']) ?>"></div>
          <div class="col-md-4"><input class="form-control" name="linkedin_url" placeholder="URL LinkedIn" value="<?= e($profile['linkedin_url']) ?>"></div>
          <div class="col-md-6"><input class="form-control" name="youtube_url" placeholder="URL YouTube" value="<?= e($profile['youtube_url']) ?>"></div>
          <div class="col-md-6"><input class="form-control" name="website_url" placeholder="URL site associe" value="<?= e($profile['website_url']) ?>"></div>
          <div class="col-md-4"><label class="form-label">Photo profil 1</label><input class="form-control" type="file" name="profile_photo" accept="image/*"></div>
          <div class="col-md-4"><label class="form-label">Photo profil 2</label><input class="form-control" type="file" name="profile_photo_2" accept="image/*"></div>
          <div class="col-md-4"><label class="form-label">CV (PDF)</label><input class="form-control" type="file" name="cv_file" accept="application/pdf"></div>
          <div class="col-md-6">
            <label class="form-label d-block">Photo profil 1 actuelle</label>
            <?php if (!empty($profile['profile_photo'])): ?>
              <img src="<?= e(BASE_URL . '/uploads/media/' . $profile['profile_photo']) ?>" alt="Photo profil 1" class="img-thumbnail mb-2" style="max-width:160px;">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remove_photo_1" id="remove_photo_1" value="1">
                <label class="form-check-label" for="remove_photo_1">Supprimer photo 1 a l'enregistrement</label>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">Aucune photo 1.</p>
            <?php endif; ?>
          </div>
          <div class="col-md-6">
            <label class="form-label d-block">Photo profil 2 actuelle</label>
            <?php if (!empty($profile['profile_photo_2'])): ?>
              <img src="<?= e(BASE_URL . '/uploads/media/' . $profile['profile_photo_2']) ?>" alt="Photo profil 2" class="img-thumbnail mb-2" style="max-width:160px;">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remove_photo_2" id="remove_photo_2" value="1">
                <label class="form-check-label" for="remove_photo_2">Supprimer photo 2 a l'enregistrement</label>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">Aucune photo 2.</p>
            <?php endif; ?>
          </div>
        </div>
        <button class="btn btn-rdn mt-3" type="submit">Mettre a jour</button>
      </form>
    </section>

    <section class="card-rdn p-4 mb-4">
      <h2 class="h5 fw-bold">Publier une actualite</h2>
      <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="new_post">
        <div class="col-md-4"><input class="form-control" name="title" placeholder="Titre" required></div>
        <div class="col-md-6"><input class="form-control" name="content" placeholder="Contenu" required></div>
        <div class="col-md-2"><button class="btn btn-rdn w-100" type="submit">Ajouter</button></div>
      </form>
      <hr>
      <?php foreach ($posts as $p): ?>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
          <span><?= e($p['title']) ?> <small class="text-muted">(<?= e($p['created_at']) ?>)</small></span>
          <form method="post" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_post">
            <input type="hidden" name="post_id" value="<?= (int) $p['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
          </form>
        </div>
      <?php endforeach; ?>
    </section>

    <section class="card-rdn p-4 mb-4">
      <h2 class="h5 fw-bold">Aux Origines de mon parcours</h2>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="new_portfolio">
        <div class="col-md-3"><input class="form-control" name="p_title" placeholder="Titre" required></div>
        <div class="col-md-3"><input class="form-control" name="p_description" placeholder="Description"></div>
        <div class="col-md-3"><input class="form-control" name="p_url" placeholder="URL projet"></div>
        <div class="col-md-2"><input class="form-control" type="file" name="p_image" accept="image/*"></div>
        <div class="col-md-1"><button class="btn btn-rdn w-100" type="submit">+</button></div>
      </form>
      <hr>
      <?php foreach ($portfolio as $it): ?>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
          <span><?= e($it['title']) ?></span>
          <form method="post" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_portfolio">
            <input type="hidden" name="portfolio_id" value="<?= (int) $it['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
          </form>
        </div>
      <?php endforeach; ?>
    </section>

    <section class="card-rdn p-4 mb-4">
      <h2 class="h5 fw-bold">Galerie multimedia</h2>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="new_media">
        <div class="col-md-3"><input class="form-control" name="m_title" placeholder="Titre" required></div>
        <div class="col-md-3">
          <select class="form-select" name="m_type">
            <option value="photo">Photo</option>
            <option value="audio">Audio</option>
            <option value="video">Video</option>
          </select>
        </div>
        <div class="col-md-4"><input class="form-control" type="file" name="m_file" required></div>
        <div class="col-md-2"><button class="btn btn-rdn w-100" type="submit">Ajouter</button></div>
      </form>
      <hr>
      <?php foreach ($media as $m): ?>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
          <span><?= e($m['title']) ?> <small class="text-muted">(<?= e($m['media_type']) ?>)</small></span>
          <form method="post" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_media">
            <input type="hidden" name="media_id" value="<?= (int) $m['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
          </form>
        </div>
      <?php endforeach; ?>
    </section>

    <section class="card-rdn p-4 mb-4">
      <h2 class="h5 fw-bold">Changer le mot de passe</h2>
      <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="change_password">
        <div class="col-md-4"><input class="form-control" type="password" name="current_password" placeholder="Mot de passe actuel" required></div>
        <div class="col-md-4"><input class="form-control" type="password" name="new_password" placeholder="Nouveau mot de passe" required></div>
        <div class="col-md-4"><input class="form-control" type="password" name="confirm_password" placeholder="Confirmer le nouveau mot de passe" required></div>
        <div class="col-12"><button class="btn btn-rdn" type="submit">Mettre a jour le mot de passe</button></div>
      </form>
    </section>

    <section class="card-rdn p-4 mb-4">
      <h2 class="h5 fw-bold">Changer le nom utilisateur</h2>
      <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="change_username">
        <div class="col-md-6"><input class="form-control" type="text" name="new_username" placeholder="Nouveau nom utilisateur" required></div>
        <div class="col-md-6"><button class="btn btn-rdn w-100" type="submit">Mettre a jour le nom</button></div>
      </form>
    </section>

    <section class="card-rdn p-4">
      <h2 class="h5 fw-bold">Abonnes recents</h2>
      <?php foreach ($subscribers as $s): ?>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2 gap-2">
          <span><?= e($s['email']) ?> <small class="text-muted">(<?= e($s['created_at']) ?>)</small></span>
          <form method="post" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_subscriber">
            <input type="hidden" name="subscriber_id" value="<?= (int) $s['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
          </form>
        </div>
      <?php endforeach; ?>
    </section>
  </div>
</body>
</html>
