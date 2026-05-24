-- ===================================
-- BERBER RANDEVU SİSTEMİ - VERİTABANI
-- ===================================

CREATE DATABASE IF NOT EXISTS berber_sistemi CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE berber_sistemi;

-- Admin tablosu
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    sifre_hash VARCHAR(255) NOT NULL,
    ad_soyad VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Varsayılan admin: kullanici=admin, sifre=admin123
INSERT INTO admin (kullanici_adi, sifre_hash, ad_soyad) VALUES
('admin', '$2y$10$3Mjur38LuhOdlgnFsTCG0.7j9RxbCt4PfQALmP66JyYPOdHYZhS/S', 'Admin Berber');

-- Hizmetler tablosu
CREATE TABLE IF NOT EXISTS hizmetler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(100) NOT NULL,
    sure_dakika INT NOT NULL DEFAULT 30,
    fiyat DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    aciklama TEXT NULL,
    aktif TINYINT(1) DEFAULT 1,
    sira INT DEFAULT 0
);

INSERT INTO hizmetler (ad, sure_dakika, fiyat, aciklama, sira) VALUES
('Saç Kesimi', 30, 150.00, 'Yüz şeklinize özel tasarlanmış, modern tekniklerle profesyonel saç kesimi hizmeti.', 1),
('Sakal Düzeltme', 20, 100.00, 'Düzenli hat çalışması ve özel bakım ürünleriyle tam bakımlı, temiz bir görünüm.', 2),
('Saç + Sakal', 50, 230.00, 'Saç kesimi ve sakal bakımını bir arada sunan komple erkek bakım paketi.', 3),
('Saç Boyama', 60, 350.00, 'Kalıcı veya geçici renk seçenekleriyle istediğiniz tona ve görünüme kavuşun.', 4),
('Cilt Bakımı', 45, 200.00, 'Derin temizlik, gözenek sıkılaştırma ve besleyici maskelerle sağlıklı cilt.', 5);

-- Çalışma saatleri (0=Pazar, 1=Pazartesi, ..., 6=Cumartesi)
CREATE TABLE IF NOT EXISTS calisma_saatleri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gun TINYINT NOT NULL COMMENT '0=Pazar, 1=Pzt, 2=Sal, 3=Car, 4=Per, 5=Cum, 6=Cmt',
    acilis TIME NOT NULL DEFAULT '09:00:00',
    kapanis TIME NOT NULL DEFAULT '19:00:00',
    aktif TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_gun (gun)
);

INSERT INTO calisma_saatleri (gun, acilis, kapanis, aktif) VALUES
(0, '10:00:00', '18:00:00', 0),
(1, '09:00:00', '19:00:00', 1),
(2, '09:00:00', '19:00:00', 1),
(3, '09:00:00', '19:00:00', 1),
(4, '09:00:00', '19:00:00', 1),
(5, '09:00:00', '19:00:00', 1),
(6, '10:00:00', '17:00:00', 1);

-- Tatil günleri
CREATE TABLE IF NOT EXISTS tatil_gunleri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tarih DATE NOT NULL UNIQUE,
    aciklama VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Randevular
CREATE TABLE IF NOT EXISTS randevular (
    id INT AUTO_INCREMENT PRIMARY KEY,
    musteri_ad VARCHAR(50) NOT NULL,
    musteri_soyad VARCHAR(50) NOT NULL,
    musteri_telefon VARCHAR(20) NOT NULL,
    hizmet_id INT NOT NULL,
    tarih DATE NOT NULL,
    baslangic_saati TIME NOT NULL,
    bitis_saati TIME NOT NULL,
    durum ENUM('beklemede','onaylandi','iptal','tamamlandi') DEFAULT 'onaylandi',
    notlar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tarih (tarih),
    INDEX idx_telefon (musteri_telefon),
    FOREIGN KEY (hizmet_id) REFERENCES hizmetler(id) ON DELETE RESTRICT
);
