<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

admin_giris_kontrol();

$action = $_POST['action'] ?? '';
$pdo    = db();

switch ($action) {
    case 'guncelle':
        // Tüm günleri güncelle
        for ($g = 0; $g <= 6; $g++) {
            $aktif   = isset($_POST["aktif_{$g}"]) ? 1 : 0;
            $acilis  = $_POST["acilis_{$g}"]  ?? '09:00';
            $kapanis = $_POST["kapanis_{$g}"] ?? '19:00';
            $pdo->prepare("UPDATE calisma_saatleri SET aktif=?, acilis=?, kapanis=? WHERE gun=?")
                ->execute([$aktif, $acilis, $kapanis, $g]);
        }
        json_cevap(['basari' => true]);
        break;

    case 'tatil_ekle':
        $tarih = trim($_POST['tarih'] ?? '');
        $acik  = trim($_POST['aciklama'] ?? '');
        if (!$tarih) json_cevap(['hata' => 'Tarih gerekli'], 400);
        try {
            $pdo->prepare("INSERT INTO tatil_gunleri (tarih, aciklama) VALUES (?,?)")->execute([$tarih, $acik ?: null]);
            json_cevap(['basari' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            json_cevap(['hata' => 'Bu tarih zaten tatil olarak ekli'], 409);
        }
        break;

    case 'tatil_sil':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) json_cevap(['hata' => 'ID gerekli'], 400);
        $pdo->prepare("DELETE FROM tatil_gunleri WHERE id = ?")->execute([$id]);
        json_cevap(['basari' => true]);
        break;

    default:
        json_cevap(['hata' => 'Bilinmeyen işlem'], 400);
}
