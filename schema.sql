CREATE DATABASE IF NOT EXISTS tks_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tks_site;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS profile (
  id TINYINT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  status_message VARCHAR(255) NOT NULL,
  bio TEXT NOT NULL,
  profile_photo VARCHAR(255) DEFAULT '',
  profile_photo_2 VARCHAR(255) DEFAULT '',
  cv_file VARCHAR(255) DEFAULT '',
  birth_date VARCHAR(20) DEFAULT '',
  birth_place VARCHAR(120) DEFAULT '',
  country VARCHAR(120) DEFAULT '',
  province VARCHAR(120) DEFAULT '',
  territory VARCHAR(120) DEFAULT '',
  sector VARCHAR(120) DEFAULT '',
  grouping VARCHAR(120) DEFAULT '',
  village VARCHAR(120) DEFAULT '',
  father_name VARCHAR(120) DEFAULT '',
  mother_name VARCHAR(120) DEFAULT '',
  primary_education TEXT,
  secondary_education TEXT,
  university_education TEXT,
  life_history TEXT,
  facebook_url VARCHAR(255) DEFAULT '',
  linkedin_url VARCHAR(255) DEFAULT '',
  youtube_url VARCHAR(255) DEFAULT '',
  website_url VARCHAR(255) DEFAULT '',
  phone_1 VARCHAR(50) NOT NULL,
  phone_2 VARCHAR(50) NOT NULL,
  email VARCHAR(150) NOT NULL,
  whatsapp VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS portfolio_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  project_url VARCHAR(255) DEFAULT '',
  image_path VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS media_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  media_type ENUM('photo', 'audio', 'video') NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  content TEXT NOT NULL,
  likes_count INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS site_stats (
  stat_key VARCHAR(50) PRIMARY KEY,
  stat_value INT NOT NULL DEFAULT 0
);

INSERT INTO site_stats (stat_key, stat_value)
VALUES ('visitors', 0)
ON DUPLICATE KEY UPDATE stat_value = stat_value;

INSERT INTO profile (
  id, name, status_message, bio, phone_1, phone_2, email, whatsapp
) VALUES (
  1,
  'TELEMA KIMBANGU (TKS)',
  'Bienvenue sur mon espace digital.',
  'Je partage ici mon parcours, mes projets et mes actualites.',
  '+243 997 972 669',
  '+243 893 473 370',
  'Rufilsdiakugilette@gmail.com',
  '243997972669'
)
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO users (username, password_hash)
VALUES ('admin', '$2y$10$MrTCehOJJ/Jv6gTma0ZjaOQ2HS.zV78XeTOBvww03D2tWb1ItDwja')
ON DUPLICATE KEY UPDATE username = username;

