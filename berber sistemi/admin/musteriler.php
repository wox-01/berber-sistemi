<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
admin_giris_kontrol();

$pdo = db();
$topbar_baslik = 'Müşteriler';
$topbar_alt    = 'Müşteri listesi';

$arama = trim($_GET['arama'] ?? '');

$sql  = "
    SELECT
        musteri_telefon,
        MAX(musteri_ad) AS ad,
        MAX(musteri_soyad) AS soyad,
        COUNT(*) AS toplam_randevu,
        SUM(h.fiyat) AS toplam_harcama,
        MAX(r.tarih) AS son_randevu,
        MAX(r.created_at) AS son_kayit
    FROM randevular r
    JOIN hizmetler h ON r.hizmet_id = h.id
    WHERE r.durum != 'iptal'
";
$params = [];
if ($arama) {
    $sql .= " AND (r.musteri_ad LIKE ? OR r.musteri_soyad LIKE ? OR r.musteri_telefon LIKE ?)";
    $like = "%{$arama}%";
    $params = [$like,$like,$like];
}
$sql .= " GROUP BY musteri_telefon ORDER BY son_kayit DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$musteriler = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Müşteriler - Admin</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <div class="main-content">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <div class="page-content">

      <div class="table-card">
        <div class="table-header">
          <h3>Müşteriler (<?= count($musteriler) ?>)</h3>
          <form method="GET" style="display:flex;gap:0.5rem;">
            <input type="text" name="arama" class="filter-input" placeholder="🔍 İsim, telefon..." value="<?= htmlspecialchars($arama) ?>">
            <button type="submit" class="btn btn-primary" style="padding:0.45rem 1rem;font-size:0.85rem;">Ara</button>
            <?php if ($arama): ?><a href="/admin/musteriler.php" class="btn btn-secondary" style="padding:0.45rem 0.8rem;font-size:0.85rem;">✕</a><?php endif; ?>
          </form>
        </div>
        <div class="table-wrap">
          <?php if (empty($musteriler)): ?>
          <div class="table-empty">
            <div class="empty-icon">👥</div>
            <p>Müşteri bulunamadı</p>
          </div>
          <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Ad Soyad</th>
                <th>Telefon</th>
                <th>Randevu Sayısı</th>
                <th>Toplam Harcama</th>
                <th>Son Randevu</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($musteriler as $m): ?>
              <tr data-arama="<?= htmlspecialchars(strtolower($m['ad'].' '.$m['soyad'].' '.$m['musteri_telefon'])) ?>">
                <td class="td-isim">
                  <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(212,175,55,0.15);display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;color:var(--gold);">
                      <?= strtoupper(mb_substr($m['ad'],0,1,'UTF-8')) ?>
                    </div>
                    <?= htmlspecialchars($m['ad'] . ' ' . $m['soyad']) ?>
                  </div>
                </td>
                <td class="td-tel"><?= htmlspecialchars($m['musteri_telefon']) ?></td>
                <td><strong><?= $m['toplam_randevu'] ?></strong></td>
                <td style="color:var(--gold);"><?= number_format($m['toplam_harcama'],0,',','.') ?> ₺</td>
                <td style="font-size:0.85rem;"><?= date('d.m.Y', strtotime($m['son_randevu'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99;"></div>
<script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
