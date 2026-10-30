-- ============================================================
-- SQL Script Schema & Seeder untuk MySQL / MariaDB
-- Aplikasi Pengaduan Perpustakaan Bangkalan (DISPUSIP)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Buat Tabel Users
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'petugas', 'user') NOT NULL DEFAULT 'user',
    `avatar` VARCHAR(255) NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `google_id` VARCHAR(255) NULL DEFAULT NULL,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_users_google_id` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Buat Tabel Password Resets
CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX `idx_password_resets_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Buat Tabel Failed Jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(255) NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Buat Tabel Personal Access Tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL UNIQUE,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_tokenable` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Buat Tabel Categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(255) NOT NULL DEFAULT 'bi-tag',
    `color` VARCHAR(50) NOT NULL DEFAULT '#3498db',
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Buat Tabel Complaints
CREATE TABLE IF NOT EXISTS `complaints` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tracking_code` VARCHAR(10) NOT NULL UNIQUE,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `reporter_name` VARCHAR(255) NOT NULL,
    `reporter_phone` VARCHAR(20) NOT NULL,
    `reporter_email` VARCHAR(255) NULL DEFAULT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `location` VARCHAR(255) NULL DEFAULT NULL,
    `status` ENUM('pending', 'in_progress', 'resolved', 'rejected') NOT NULL DEFAULT 'pending',
    `priority` ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
    `rejection_reason` TEXT NULL DEFAULT NULL,
    `resolved_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_complaints_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_complaints_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Buat Tabel Complaint Attachments
CREATE TABLE IF NOT EXISTS `complaint_attachments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `complaint_id` BIGINT UNSIGNED NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_attachments_complaint` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Buat Tabel Complaint Responses
CREATE TABLE IF NOT EXISTS `complaint_responses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `complaint_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `message` TEXT NOT NULL,
    `status_change` ENUM('pending', 'in_progress', 'resolved', 'rejected') NULL DEFAULT NULL,
    `is_public` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_responses_complaint` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_responses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Pencatatan Migrations (Agar sinkron dengan Laravel)
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2024_01_01_000001_create_categories_table', 1),
('2024_01_01_000002_create_complaints_table', 1),
('2024_01_01_000003_create_complaint_attachments_table', 1),
('2024_01_01_000004_create_complaint_responses_table', 1),
('2026_10_27_030807_add_google_id_to_users_table', 1)
ON DUPLICATE KEY UPDATE `batch` = VALUES(`batch`);

-- ============================================================
-- SEED DATA AWAL (Kategori & Akun Admin/Petugas)
-- ============================================================

-- Data Users Default (Admin & Petugas)
-- Password Admin: Admin@Perpus2024!
-- Password Petugas: Petugas@2024!
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`)
VALUES 
(
    1,
    'Administrator', 
    'admin@perpusbangkalan.go.id', 
    '$2y$10$ReCxwCY1xC/m1eA2RRVI1.VNDxW6dD//CnHX5/E8xnZMmK1FtXp/K', 
    'admin', 
    1, 
    NOW(), 
    NOW()
),
(
    2,
    'Petugas Perpustakaan', 
    'petugas@perpusbangkalan.go.id', 
    '$2y$10$oYM1bDC.88264t33rBtKe.dJKmQ.JhRxuKRDCJfe3Gj.ezlAGNAWq', 
    'petugas', 
    1, 
    NOW(), 
    NOW()
)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Data Kategori Awal
INSERT INTO `categories` (`id`, `name`, `icon`, `color`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES
(1, 'Fasilitas & Sarana Prasarana', 'bi-building', '#3498db', 'Pengaduan terkait kondisi gedung, meja, kursi, AC, toilet, dan fasilitas fisik lainnya.', 1, 1, NOW(), NOW()),
(2, 'Pelayanan Petugas', 'bi-person-badge', '#e74c3c', 'Pengaduan terkait sikap, profesionalitas, dan kualitas pelayanan petugas perpustakaan.', 1, 2, NOW(), NOW()),
(3, 'Koleksi Buku & Referensi', 'bi-book', '#2ecc71', 'Pengaduan tentang kelengkapan, kondisi, atau ketersediaan buku dan bahan referensi.', 1, 3, NOW(), NOW()),
(4, 'Layanan Digital & Website', 'bi-laptop', '#9b59b6', 'Pengaduan terkait sistem digital, website, e-library, atau layanan online perpustakaan.', 1, 4, NOW(), NOW()),
(5, 'Kebersihan & Kenyamanan', 'bi-stars', '#f39c12', 'Pengaduan mengenai kebersihan ruangan, kenyamanan, dan lingkungan perpustakaan.', 1, 5, NOW(), NOW()),
(6, 'Jadwal & Jam Operasional', 'bi-clock', '#1abc9c', 'Pengaduan terkait jam buka tutup, jadwal layanan, dan ketidaksesuaian operasional.', 1, 6, NOW(), NOW()),
(7, 'Saran & Masukan', 'bi-lightbulb', '#e67e22', 'Saran dan masukan untuk pengembangan dan peningkatan layanan perpustakaan.', 1, 7, NOW(), NOW()),
(8, 'Lainnya', 'bi-chat-dots', '#95a5a6', 'Pengaduan atau masukan yang tidak termasuk dalam kategori di atas.', 1, 8, NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

SET FOREIGN_KEY_CHECKS = 1;
