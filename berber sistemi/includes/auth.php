<?php
session_start();
require_once __DIR__ . '/config.php';

function admin_giris_kontrol(): void {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . '/admin/index.php');
        exit;
    }
}

function admin_giris_yap(string $kullanici_adi, string $sifre): bool {
    require_once __DIR__ . '/db.php';
    $stmt = db()->prepare("SELECT id, sifre_hash, ad_soyad FROM admin WHERE kullanici_adi = ?");
    $stmt->execute([$kullanici_adi]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($sifre, $admin['sifre_hash'])) {
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_ad_soyad'] = $admin['ad_soyad'];
        return true;
    }
    return false;
}

function admin_cikis(): void {
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}
