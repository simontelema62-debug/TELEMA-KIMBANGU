<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

increment_visitors_once();
$profile = get_profile();
$posts = db()->query('SELECT id, title, content, created_at, likes_count FROM posts ORDER BY created_at DESC LIMIT 12')->fetchAll();
$portfolio = db()->query('SELECT id, title, description, project_url, image_path FROM portfolio_items ORDER BY created_at DESC')->fetchAll();
$media = db()->query('SELECT id, title, media_type, file_path FROM media_items ORDER BY created_at DESC')->fetchAll();
$visitors = stat_value('visitors');
$subscribers = (int) db()->query('SELECT COUNT(*) AS c FROM subscribers')->fetch()['c'];
$totalLikes = (int) db()->query('SELECT COALESCE(SUM(likes_count),0) AS t FROM posts')->fetch()['t'];
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= e(BASE_URL) ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
  <header class="hero-section">
    <nav class="navbar navbar-expand-lg nav-rdn">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#">TELEMA KIMBANGU</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Toggle">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="#profil" data-i18n="nav_profile">Profil</a></li>
            <li class="nav-item"><a class="nav-link" href="#cv" data-i18n="nav_cv">CV</a></li>
            <li class="nav-item"><a class="nav-link" href="#portfolio" data-i18n="nav_portfolio">Portfolio</a></li>
            <li class="nav-item"><a class="nav-link" href="#media" data-i18n="nav_media">Multimedia</a></li>
            <li class="nav-item"><a class="nav-link" href="#actus" data-i18n="nav_news">Actualites</a></li>
            <li class="nav-item"><a class="nav-link" href="#contact" data-i18n="nav_contact">Contact</a></li>
          </ul>
          <div class="nav-tools ms-lg-3 mt-3 mt-lg-0">
            <button class="btn btn-sm btn-outline-rdn" id="themeToggle" type="button">Mode sombre</button>
            <div class="btn-group btn-group-sm ms-2" role="group" aria-label="Language">
              <button class="btn btn-outline-rdn lang-btn" type="button" data-lang="fr">FR</button>
              <button class="btn btn-outline-rdn lang-btn" type="button" data-lang="en">EN</button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="container py-5" id="profil">
      <div class="row align-items-center g-4">
        <div class="col-md-4 text-center">
          <?php if (!empty($profile['profile_photo'])): ?>
            <div class="profile-visual<?= !empty($profile['profile_photo_2']) ? ' has-alt' : '' ?>">
              <img class="profile-img profile-img-main" src="<?= e(BASE_URL . '/uploads/media/' . $profile['profile_photo']) ?>" alt="Photo profil principale">
              <?php if (!empty($profile['profile_photo_2'])): ?>
                <img class="profile-img profile-img-alt" src="<?= e(BASE_URL . '/uploads/media/' . $profile['profile_photo_2']) ?>" alt="Photo profil secondaire">
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="profile-placeholder">TKS</div>
          <?php endif; ?>
        </div>
        <div class="col-md-8">
          <h1 class="display-5 fw-bold"><?= e($profile['name']) ?></h1>
          <p class="lead status-pill"><?= e($profile['status_message']) ?></p>
          <p><?= nl2br(e($profile['bio'])) ?></p>
        </div>
      </div>
    </div>
  </header>

  <main class="container py-5">
    <section id="cv" class="mb-5 card-rdn p-4">
      <h2 class="h3 fw-bold" data-i18n="cv_title">Curriculum Vitae</h2>
      <p data-i18n="cv_desc">Consultez mon CV en ligne ou telechargez-le.</p>
      <?php if (!empty($profile['cv_file'])): ?>
        <a class="btn btn-rdn me-2" target="_blank" rel="noopener noreferrer" href="<?= e(BASE_URL . '/uploads/cv/' . $profile['cv_file']) ?>" data-i18n="cv_view">Voir le CV</a>
        <a class="btn btn-outline-rdn" download href="<?= e(BASE_URL . '/uploads/cv/' . $profile['cv_file']) ?>" data-i18n="download">Telecharger</a>
      <?php else: ?>
        <p class="text-muted mb-0" data-i18n="cv_unavailable">CV non disponible pour le moment.</p>
      <?php endif; ?>
    </section>

    <section id="portfolio" class="mb-5">
      <h2 class="h3 fw-bold mb-3" data-i18n="portfolio_title">Portfolio</h2>
      <?php if (
        !empty($profile['birth_date']) ||
        !empty($profile['birth_place']) ||
        !empty($profile['country']) ||
        !empty($profile['province']) ||
        !empty($profile['territory']) ||
        !empty($profile['sector']) ||
        !empty($profile['grouping']) ||
        !empty($profile['village']) ||
        !empty($profile['father_name']) ||
        !empty($profile['mother_name']) ||
        !empty($profile['primary_education']) ||
        !empty($profile['secondary_education']) ||
        !empty($profile['university_education']) ||
        !empty($profile['life_history'])
      ): ?>
        <article class="card-rdn p-4 mb-4">
          <h3 class="h5 fw-bold" data-i18n="history_title">Bref historique</h3>
          <div class="row g-3">
            <?php if (!empty($profile['birth_date'])): ?>
              <div class="col-md-4"><strong data-i18n="birth_date">Date de naissance</strong><div><?= e($profile['birth_date']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['birth_place'])): ?>
              <div class="col-md-4"><strong data-i18n="birth_place">Lieu de naissance</strong><div><?= e($profile['birth_place']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['country'])): ?>
              <div class="col-md-4"><strong data-i18n="country">Pays</strong><div><?= e($profile['country']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['province'])): ?>
              <div class="col-md-4"><strong data-i18n="province">Province</strong><div><?= e($profile['province']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['territory'])): ?>
              <div class="col-md-4"><strong data-i18n="territory">Territoire</strong><div><?= e($profile['territory']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['sector'])): ?>
              <div class="col-md-4"><strong data-i18n="sector">Secteur</strong><div><?= e($profile['sector']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['grouping'])): ?>
              <div class="col-md-4"><strong data-i18n="grouping">Groupement</strong><div><?= e($profile['grouping']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['village'])): ?>
              <div class="col-md-4"><strong data-i18n="village">Village</strong><div><?= e($profile['village']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['father_name'])): ?>
              <div class="col-md-4"><strong data-i18n="father_name">Nom du pere</strong><div><?= e($profile['father_name']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['mother_name'])): ?>
              <div class="col-md-4"><strong data-i18n="mother_name">Nom de la mere</strong><div><?= e($profile['mother_name']) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['primary_education'])): ?>
              <div class="col-md-6"><strong data-i18n="primary_education">Parcours primaire</strong><div><?= nl2br(e($profile['primary_education'])) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['secondary_education'])): ?>
              <div class="col-md-6"><strong data-i18n="secondary_education">Parcours secondaire</strong><div><?= nl2br(e($profile['secondary_education'])) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['university_education'])): ?>
              <div class="col-12"><strong data-i18n="university_education">Parcours universitaire</strong><div><?= nl2br(e($profile['university_education'])) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($profile['life_history'])): ?>
              <div class="col-12"><strong data-i18n="life_history">Bref historique</strong><div><?= nl2br(e($profile['life_history'])) ?></div></div>
            <?php endif; ?>
          </div>
        </article>
      <?php endif; ?>
      <div class="row g-3">
        <?php if ($portfolio): foreach ($portfolio as $item): ?>
          <div class="col-md-4">
            <article class="card-rdn h-100">
              <?php if (!empty($item['image_path'])): ?>
                <img src="<?= e(BASE_URL . '/uploads/media/' . $item['image_path']) ?>" class="card-img-top" alt="<?= e($item['title']) ?>">
              <?php endif; ?>
              <div class="p-3">
                <h3 class="h5 fw-bold"><?= e($item['title']) ?></h3>
                <p><?= e($item['description']) ?></p>
                <?php if (!empty($item['project_url'])): ?>
                  <a class="link-rdn" target="_blank" rel="noopener" href="<?= e($item['project_url']) ?>" data-i18n="view_project">Voir le projet</a>
                <?php endif; ?>
              </div>
            </article>
          </div>
        <?php endforeach; else: ?>
          <p class="text-muted" data-i18n="no_projects">Aucun projet pour le moment.</p>
        <?php endif; ?>
      </div>
    </section>
    <section id="media" class="mb-5">
      <h2 class="h3 fw-bold mb-3" data-i18n="media_title">Galerie multimedia</h2>
      <div class="row g-3">
        <?php if ($media): foreach ($media as $m): ?>
          <div class="col-md-4">
            <div class="card-rdn p-3 h-100">
              <h3 class="h6 fw-bold"><?= e($m['title']) ?></h3>
              <?php if ($m['media_type'] === 'photo'): ?>
                <img class="img-fluid rounded" src="<?= e(BASE_URL . '/uploads/media/' . $m['file_path']) ?>" alt="<?= e($m['title']) ?>">
              <?php elseif ($m['media_type'] === 'video'): ?>
                <video controls class="w-100 rounded"><source src="<?= e(BASE_URL . '/uploads/media/' . $m['file_path']) ?>"></video>
              <?php else: ?>
                <audio controls class="w-100"><source src="<?= e(BASE_URL . '/uploads/media/' . $m['file_path']) ?>"></audio>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; else: ?>
          <p class="text-muted" data-i18n="no_media">Aucun contenu multimedia.</p>
        <?php endif; ?>
      </div>
    </section>

    <section id="actus" class="mb-5">
      <h2 class="h3 fw-bold mb-3" data-i18n="news_title">Informations et actualites</h2>
      <div class="row g-3">
        <?php if ($posts): foreach ($posts as $post): ?>
          <div class="col-md-6">
            <article class="card-rdn p-3 h-100">
              <h3 class="h5 fw-bold"><?= e($post['title']) ?></h3>
              <p><?= nl2br(e($post['content'])) ?></p>
              <small class="text-muted d-block mb-2"><span data-i18n="published_on">Publie le</span> <?= e(date('d/m/Y H:i', strtotime($post['created_at']))) ?></small>
              <div class="d-flex gap-2 align-items-center">
                <form method="post" action="<?= e(BASE_URL) ?>/like_post.php" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                  <button class="btn btn-sm btn-outline-rdn" type="submit"><span data-i18n="like">J'aime</span> (<?= (int) $post['likes_count'] ?>)</button>
                </form>
                <button class="btn btn-sm btn-rdn-soft share-btn" data-title="<?= e($post['title']) ?>" data-i18n="share">Partager</button>
              </div>
            </article>
          </div>
        <?php endforeach; else: ?>
          <p class="text-muted" data-i18n="no_news">Aucune actualite pour le moment.</p>
        <?php endif; ?>
      </div>
    </section>

    <section class="mb-5 card-rdn p-4">
      <h2 class="h3 fw-bold" data-i18n="interactions_title">Interactions</h2>
      <div class="row g-3 text-center">
        <div class="col-md-4"><div class="stat-box"><strong><?= $visitors ?></strong><span data-i18n="visitors">Visiteurs</span></div></div>
        <div class="col-md-4"><div class="stat-box"><strong><?= $subscribers ?></strong><span data-i18n="subscribers">Abonnes</span></div></div>
        <div class="col-md-4"><div class="stat-box"><strong><?= $totalLikes ?></strong><span data-i18n="likes">J'aime</span></div></div>
      </div>
      <form class="mt-3" method="post" action="<?= e(BASE_URL) ?>/subscribe.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="input-group">
          <input class="form-control" type="email" name="email" placeholder="Votre email pour vous abonner" data-i18n-placeholder="subscribe_placeholder" required>
          <button class="btn btn-rdn" type="submit" data-i18n="subscribe">S'abonner</button>
        </div>
      </form>
    </section>

    <section class="mb-5 card-rdn p-4">
      <h2 class="h3 fw-bold" data-i18n="partners_title">Association avec d'autres sites</h2>
      <p class="mb-2" data-i18n="partners_desc">Liens vers d'autres plateformes :</p>
      <a class="link-rdn me-3" target="_blank" rel="noopener noreferrer" href="<?= e($profile['website_url'] ?: 'https://github.com') ?>" data-i18n="partner_1">Site partenaire 1</a>
      <a class="link-rdn me-3" target="_blank" rel="noopener noreferrer" href="https://www.linkedin.com">LinkedIn</a>
      <a class="link-rdn" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com">YouTube</a>
    </section>
  </main>

  <footer id="contact" class="footer-rdn py-4">
    <div class="container">
      <h2 class="h4 fw-bold"><i class="bi bi-geo-alt-fill me-1"></i><span data-i18n="contact_title">Contact</span></h2>
      <div class="row g-3 mb-3">
        <div class="col-md-6 col-lg-3">
          <article class="contact-channel card-rdn p-3 h-100">
            <div class="contact-channel-icon icon-whatsapp"><i class="bi bi-whatsapp"></i></div>
            <h3 class="h6 fw-bold mb-1">WhatsApp</h3>
            <a target="_blank" rel="noopener noreferrer" href="https://wa.me/<?= e(preg_replace('/\D+/', '', $profile['whatsapp'])) ?>" data-i18n="write_directly">Ecrire directement</a>
          </article>
        </div>
        <div class="col-md-6 col-lg-3">
          <article class="contact-channel card-rdn p-3 h-100">
            <div class="contact-channel-icon icon-facebook"><i class="bi bi-facebook"></i></div>
            <h3 class="h6 fw-bold mb-1">Facebook</h3>
            <?php if (!empty($profile['facebook_url'])): ?>
              <a target="_blank" rel="noopener noreferrer" href="<?= e($profile['facebook_url']) ?>" data-i18n="visit_page">Visiter la page</a>
            <?php else: ?>
              <span class="text-muted" data-i18n="not_available">Non disponible</span>
            <?php endif; ?>
          </article>
        </div>
        <div class="col-md-6 col-lg-3">
          <article class="contact-channel card-rdn p-3 h-100">
            <div class="contact-channel-icon icon-youtube"><i class="bi bi-youtube"></i></div>
            <h3 class="h6 fw-bold mb-1">YouTube</h3>
            <?php if (!empty($profile['youtube_url'])): ?>
              <a target="_blank" rel="noopener noreferrer" href="<?= e($profile['youtube_url']) ?>" data-i18n="visit_channel">Visiter la chaine</a>
            <?php else: ?>
              <span class="text-muted" data-i18n="not_available">Non disponible</span>
            <?php endif; ?>
          </article>
        </div>
        <div class="col-md-6 col-lg-3">
          <article class="contact-channel card-rdn p-3 h-100">
            <div class="contact-channel-icon icon-email"><i class="bi bi-envelope-fill"></i></div>
            <h3 class="h6 fw-bold mb-1" data-i18n="email_label">Email</h3>
            <a href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?></a>
          </article>
        </div>
      </div>
      <div class="mt-3"><a class="btn btn-sm btn-rdn" href="<?= e(BASE_URL) ?>/login.php" data-i18n="admin_area">Espace admin</a></div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>window.TKS_BASE_URL = <?= json_encode(BASE_URL, JSON_UNESCAPED_UNICODE) ?>;</script>
  <script src="<?= e(BASE_URL) ?>/assets/js/main.js"></script>
</body>
</html>
