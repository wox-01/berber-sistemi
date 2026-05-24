<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_cevap(['hata' => 'Yalnızca POST'], 405);
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$ad        = trim($data['ad'] ?? '');
$soyad     = trim($data['soyad'] ?? '');
$telefon   = trim($data['telefon'] ?? '');
$hizmet_id = (int)($data['hizmet_id'] ?? 0);
$tarih     = trim($data['tarih'] ?? '');
$saat      = trim($data['saat'] ?? '');
$notlar    = trim($data['notlar'] ?? '');

// Validasyon
if (!$ad || !$soyad || !$telefon || !$hizmet_id || !$tarih || !$saat) {
    json_cevap(['hata' => 'Tüm alanlar zorunlu'], 400);
}

if (!preg_match('/^[0-9\s\(\)\-\+]{10,15}$/', $telefon)) {
    json_cevap(['hata' => 'Geçersiz telefon numarası'], 400);
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih) || strtotime($tarih) < strtotime(date('Y-m-d'))) {
    json_cevap(['hata' => 'Geçersiz tarih'], 400);
}

$pdo = db();

$stmt = $pdo->prepare("SELECT sure_dakika FROM hizmetler WHERE id = ? AND aktif = 1");
$stmt->execute([$hizmet_id]);
$hizmet = $stmt->fetch();
if (!$hizmet) {
    json_cevap(['hata' => 'Hizmet bulunamadı'], 404);
}

$slotlar = mevcut_slotlari_getir($tarih, (int)$hizmet['sure_dakika']);
if (!in_array($saat, $slotlar)) {
    json_cevap(['hata' => 'Bu saat artık müsait değil'], 409);
}

$baslangic  = $tarih . ' ' . $saat;
$bitis_ts   = strtotime($baslangic) + ($hizmet['sure_dakika'] * 60);
$bitis_saat = date('H:i', $bitis_ts);

$stmt = $pdo->prepare("
    INSERT INTO randevular (musteri_ad, musteri_soyad, musteri_telefon, hizmet_id, tarih, baslangic_saati, bitis_saati, notlar)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$ad, $soyad, $telefon, $hizmet_id, $tarih, $saat, $bitis_saat, $notlar ?: null]);
$randevu_id = $pdo->lastInsertId();

json_cevap([
    'basari'     => true,
    'randevu_id' => $randevu_id,
    'mesaj'      => "Randevunuz alındı! {$tarih} tarihinde saat {$saat}'de sizi bekliyoruz."
]);
