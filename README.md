# TELEMA KIMBANGU (TKS)

Application web PHP/MySQL avec:
- site public (profil, portfolio, multimedia, actualites),
- espace admin (gestion du profil, contenus et medias),
- interactions visiteurs (likes, abonnements, statistiques).

## Prerequis
- XAMPP (Apache + MySQL + PHP 8+)
- Projet place dans `c:/xampp/htdocs/TKS`

## Installation rapide
1. Demarrer Apache et MySQL dans XAMPP.
2. Ouvrir phpMyAdmin.
3. Importer `database/schema.sql`.
4. Verifier dans `config.php`:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
5. Ouvrir:
   - Front: http://localhost/TKS/index.php
   - Admin: http://localhost/TKS/login.php
   - Alternative: http://localhost/TKS/

Identifiants admin initiaux:
- Utilisateur: `admin`
- Mot de passe: `admin123`

## Structure
- `index.php`: page publique
- `admin.php`: panneau d'administration
- `login.php` / `logout.php`: authentification admin
- `like_post.php` / `subscribe.php`: endpoints interactions
- `includes/`: utilitaires (DB, auth, helpers)
- `uploads/`: fichiers utilisateur (cv, medias)
- `database/schema.sql`: structure et donnees initiales

## Securite integree
- protection CSRF sur les formulaires sensibles
- requetes SQL preparees (PDO)
- validation upload par extension + type MIME
- sessions durcies (`HttpOnly`, `SameSite`, `Strict mode`)
- en-tetes HTTP de securite (`CSP`, `X-Frame-Options`, etc.)

## Recommandations production
1. Changer immediatement le mot de passe admin initial.
2. Passer `ENFORCE_HTTPS` a `true` dans `config.php`.
3. Restreindre les permissions d'ecriture au dossier `uploads/` uniquement.
4. Sauvegarder regulierement la base MySQL et le dossier `uploads/`.
5. Activer la journalisation serveur (Apache/PHP) pour le suivi des erreurs.
