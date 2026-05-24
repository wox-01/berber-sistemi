<?php
require_once __DIR__ . '/../includes/functions.php';

$tarih      = $_GET['tarih']     ?? '';
$hizmet_id  = (int)($_GET['hizmet_id'] ?? 0);

if (!$tarih || !$hizmet_id) {
    json_cevap(['hata' => 'Eksik parametre'], 400);
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih) || strtotime($tarih) === false) {
    json_cevap(['hata' => 'Geçersiz tarih'], 400);
}

if (strtotime($tarih) < strtotime(date('Y-m-d'))) {
    json_cevap(['slotlar' => []]);
}

$stmt = db()->prepare("SELECT sure_dakika FROM hizmetler WHERE id = ? AND aktif = 1");
$stmt->execute([$hizmet_id]);
$hizmet = $stmt->fetch();

if (!$hizmet) {
    json_cevap(['hata' => 'Hizmet bulunamadı'], 404);
}

$slotlar = mevcut_slotlari_getir($tarih, (int)$hizmet['sure_dakika']);
json_cevap(['slotlar' => $slotlar]);
