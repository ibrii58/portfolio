-- ============================================================
-- Portfolio DB — İbrahim Çam
-- Oluşturulma: 2025
-- Kullanım: phpMyAdmin veya MySQL CLI ile import edin
-- ============================================================

CREATE DATABASE IF NOT EXISTS `portfolio_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `portfolio_db`;

-- ─── PROJECTS TABLE ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `projects` (
  `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(200)     NOT NULL,
  `description`  TEXT             NOT NULL,
  `technologies` VARCHAR(500)     DEFAULT NULL,
  `github_url`   VARCHAR(500)     DEFAULT NULL,
  `live_url`     VARCHAR(500)     DEFAULT NULL,
  `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── CONTACTS TABLE ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contacts` (
  `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(200)     NOT NULL,
  `email`        VARCHAR(320)     NOT NULL,
  `subject`      VARCHAR(300)     NOT NULL,
  `message`      TEXT             NOT NULL,
  `ip_address`   VARCHAR(45)      DEFAULT NULL,
  `is_read`      TINYINT(1)       NOT NULL DEFAULT 0,
  `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── ADMINS TABLE ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admins` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(60)   NOT NULL UNIQUE,
  `password_hash` VARCHAR(255)  NOT NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── DEFAULT ADMIN (password: "password") ───────────────────
-- Güvenlik için production'da şifreyi değiştirin!
INSERT INTO `admins` (`username`, `password_hash`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ─── SAMPLE PROJECTS ────────────────────────────────────────
INSERT INTO `projects` (`title`, `description`, `technologies`, `github_url`, `live_url`) VALUES
(
  'Full-Stack Portfolio',
  'HTML5, CSS3, JavaScript, PHP ve MySQL kullanılarak geliştirilen kapsamlı portfolyo web uygulaması. Admin paneli, AJAX tabanlı içerik yükleme ve iletişim formu içermektedir.',
  'HTML5, CSS3, JavaScript, PHP, MySQL',
  'https://github.com/cibrahim58',
  NULL
),
(
  'Görev Yönetim Uygulaması',
  'Sürükle-bırak desteği olan, JavaScript ile geliştirilmiş produktivite odaklı görev yönetim uygulaması. Kategori filtreleme ve öncelik sıralaması özelliklerine sahiptir.',
  'JavaScript, CSS Grid, DOM API',
  'https://github.com/cibrahim58',
  NULL
),
(
  'Öğrenci Kayıt Sistemi',
  'PHP ve MySQL ile geliştirilen, CRUD işlemlerini destekleyen öğrenci kayıt ve takip sistemi. Arama, filtreleme ve rapor çıktısı özellikleri mevcuttur.',
  'PHP, MySQL, AJAX, Bootstrap',
  'https://github.com/cibrahim58',
  NULL
);
