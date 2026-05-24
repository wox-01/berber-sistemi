<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

admin_giris_kontrol();

$action = $_POST['action'] ?? '';
$pdo    = db();

switch ($action) {
    case 'ekle':
        $ad   = trim($_POST['ad'] ?? '');
        $sure = (int)($_POST['sure_dakika'] ?? 0);
        $fiy  = (float)($_POST['fiyat'] ?? 0);
        $sira = (int)($_POST['sira'] ?? 0);
        if (!$ad || $sure < 5) json_cevap(['hata' => 'Geçersiz veri'], 400);
        $pdo->prepare("INSERT INTO hizmetler (ad, sure_dakika, fiyat, sira) VALUES (?,?,?,?)")->execute([$ad,$sure,$fiy,$sira]);
        json_cevap(['basari' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'guncelle':
        $id   = (int)($_POST['id'] ?? 0);
        $ad   = trim($_POST['ad'] ?? '');
        $sure = (int)($_POST['sure_dakika'] ?? 0);
        $fiy  = (float)($_POST['fiyat'] ?? 0);
        $sira = (int)($_POST['sira'] ?? 0);
        if (!$id || !$ad || $sure < 5) json_cevap(['hata' => 'Geçersiz veri'], 400);
        $pdo->prepare("UPDATE hizmetler SET ad=?, sure_dakika=?, fiyat=?, sira=? WHERE id=?")->execute([$ad,$sure,$fiy,$sira,$id]);
        json_cevap(['basari' => true]);
        break;

    case 'aktif_toggle':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) json_cevap(['hata' => 'ID gerekli'], 400);
        $pdo->prepare("UPDATE hizmetler SET aktif = NOT aktif WHERE id = ?")->execute([$id]);
        json_cevap(['basari' => true]);
        break;

    case 'sil':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) json_cevap(['hata' => 'ID gerekli'], 400);
        try {
            $pdo->prepare("DELETE FROM hizmetler WHERE id = ?")->execute([$id]);
            json_cevap(['basari' => true]);
        } catch (PDOException $e) {
            json_cevap(['hata' => 'Bu hizmetle randevu var, silinemiyor'], 409);
        }
        break;

    default:
        json_cevap(['hata' => 'Bilinmeyen işlem'], 400);
}
