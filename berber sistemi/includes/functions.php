<?php
require_once __DIR__ . '/db.php';

$GUN_ADLARI = ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'];

function mevcut_slotlari_getir(string $tarih, int $hizmet_sure): array {
    $pdo = db();

    // Günün ayarlarını çek
    $gun_no = (int) date('w', strtotime($tarih));
    $stmt = $pdo->prepare("SELECT * FROM calisma_saatleri WHERE gun = ? AND aktif = 1");
    $stmt->execute([$gun_no]);
    $calisma = $stmt->fetch();
    if (!$calisma) return [];

    // Tatil kontrolü
    $stmt = $pdo->prepare("SELECT id FROM tatil_gunleri WHERE tarih = ?");
    $stmt->execute([$tarih]);
    if ($stmt->fetch()) return [];

    // O gün alınmış randevuları çek
    $stmt = $pdo->prepare("SELECT baslangic_saati, bitis_saati FROM randevular WHERE tarih = ? AND durum != 'iptal'");
    $stmt->execute([$tarih]);
    $randevular = $stmt->fetchAll();

    $acilis  = strtotime($tarih . ' ' . $calisma['acilis']);
    $kapanis = strtotime($tarih . ' ' . $calisma['kapanis']);
    $adim    = 15 * 60; // 15 dakikalık adımlar
    $sure    = $hizmet_sure * 60;

    $slotlar = [];
    $simdi   = time();

    for ($t = $acilis; $t + $sure <= $kapanis; $t += $adim) {
        $slot_bitis = $t + $sure;
        $dolu = false;

        foreach ($randevular as $r) {
            $r_bas  = strtotime($tarih . ' ' . $r['baslangic_saati']);
            $r_bit  = strtotime($tarih . ' ' . $r['bitis_saati']);
            // Çakışma kontrolü
            if ($t < $r_bit && $slot_bitis > $r_bas) {
                $dolu = true;
                break;
            }
        }

        if (!$dolu && $t > $simdi) {
            $slotlar[] = date('H:i', $t);
        }
    }

    return $slotlar;
}

function aktif_hizmetleri_getir(): array {
    $stmt = db()->query("SELECT * FROM hizmetler WHERE aktif = 1 ORDER BY sira, id");
    return $stmt->fetchAll();
}

function telefon_formatla(string $tel): string {
    $tel = preg_replace('/\D/', '', $tel);
    if (strlen($tel) === 10) {
        return '(' . substr($tel, 0, 3) . ') ' . substr($tel, 3, 3) . ' ' . substr($tel, 6, 2) . ' ' . substr($tel, 8, 2);
    }
    return $tel;
}

function json_cevap(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
