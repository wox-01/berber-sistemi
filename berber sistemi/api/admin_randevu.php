<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

admin_giris_kontrol();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id     = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

$pdo = db();

switch ($action) {
    case 'durum_guncelle':
        $durum = $_POST['durum'] ?? '';
        $izin  = ['beklemede','onaylandi','iptal','tamamlandi'];
        if (!$id || !in_array($durum, $izin)) {
            json_cevap(['hata' => 'Geçersiz istek'], 400);
        }
        $pdo->prepare("UPDATE randevular SET durum = ? WHERE id = ?")->execute([$durum, $id]);
        json_cevap(['basari' => true]);
        break;

    case 'sil':
        if (!$id) json_cevap(['hata' => 'ID gerekli'], 400);
        $pdo->prepare("DELETE FROM randevular WHERE id = ?")->execute([$id]);
        json_cevap(['basari' => true]);
        break;

    case 'liste':
        $tarih  = $_GET['tarih'] ?? '';
        $durum  = $_GET['durum'] ?? '';
        $arama  = $_GET['arama'] ?? '';
        $where  = ['1=1'];
        $params = [];

        if ($tarih) { $where[] = 'r.tarih = ?'; $params[] = $tarih; }
        if ($durum) { $where[] = 'r.durum = ?'; $params[] = $durum; }
        if ($arama) {
            $where[]  = '(r.musteri_ad LIKE ? OR r.musteri_soyad LIKE ? OR r.musteri_telefon LIKE ?)';
            $like     = "%{$arama}%";
            $params   = array_merge($params, [$like, $like, $like]);
        }

        $sql  = "SELECT r.*, h.ad AS hizmet_ad, h.fiyat FROM randevular r JOIN hizmetler h ON r.hizmet_id = h.id WHERE " . implode(' AND ', $where) . " ORDER BY r.tarih DESC, r.baslangic_saati ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        json_cevap(['randevular' => $stmt->fetchAll()]);
        break;

    default:
        json_cevap(['hata' => 'Bilinmeyen işlem'], 400);
}
