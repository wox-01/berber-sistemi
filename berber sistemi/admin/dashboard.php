<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
admin_giris_kontrol();

$pdo = db();
$bugun = date('Y-m-d');

$stat_bugun     = $pdo->prepare("SELECT COUNT(*) FROM randevular WHERE tarih = ? AND durum != 'iptal'");
$stat_bugun->execute([$bugun]);
$bugun_sayi = $stat_bugun->fetchColumn();

$stat_bekleyen  = $pdo->query("SELECT COUNT(*) FROM randevular WHERE durum = 'beklemede'")->fetchColumn();
$stat_toplam    = $pdo->query("SELECT COUNT(*) FROM randevular WHERE durum != 'iptal'")->fetchColumn();
$stat_musteri   = $pdo->query("SELECT COUNT(DISTINCT musteri_telefon) FROM randevular")->fetchColumn();

$stmt = $pdo->prepare("
    SELECT r.*, h.ad AS hizmet_ad FROM randevular r
    JOIN hizmetler h ON r.hizmet_id = h.id
    WHERE r.tarih = ?
    ORDER BY r.baslangic_saati ASC
");
$stmt->execute([$bugun]);
$bugun_randevular = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT r.tarih, COUNT(*) as sayi FROM randevular r
    WHERE r.tarih BETWEEN ? AND DATE_ADD(?, INTERVAL 7 DAY) AND r.durum != 'iptal'
    GROUP BY r.tarih ORDER BY r.tarih
");
$stmt->execute([$bugun, $bugun]);
$haftalik = $stmt->fetchAll();

$topbar_baslik = 'Dashboard';
$topbar_alt    = date('d F Y, l');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - AdonisBerber Admin</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <div class="main-content">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <div class="page-content">

      <!-- İstatistik kartları -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-icon gold">📅</div>
          <div class="stat-info">
            <h3><?= $bugun_sayi ?></h3>
            <p>Bugünkü Randevu</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red">⏳</div>
          <div class="stat-info">
            <h3><?= $stat_bekleyen ?></h3>
            <p>Bekleyen</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">✓</div>
          <div class="stat-info">
            <h3><?= $stat_toplam ?></h3>
            <p>Toplam Randevu</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue">👥</div>
          <div class="stat-info">
            <h3><?= $stat_musteri ?></h3>
            <p>Müşteri</p>
          </div>
        </div>
      </div>

      <!-- Bugünün randevuları -->
      <div class="table-card" style="margin-bottom:1.5rem;">
        <div class="table-header">
          <h3>📅 Bugünün Randevuları — <?= date('d.m.Y') ?></h3>
          <a href="<?= BASE_URL ?>/admin/randevular.php" class="btn btn-secondary" style="font-size:0.82rem;">Tümünü Gör</a>
        </div>
        <div class="table-wrap">
          <?php if (empty($bugun_randevular)): ?>
          <div class="table-empty">
            <div class="empty-icon">📭</div>
            <p>Bugün randevu yok</p>
          </div>
          <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Saat</th>
                <th>Müşteri</th>
                <th>Telefon</th>
                <th>Hizmet</th>
                <th>Durum</th>
                <th>İşlem</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bugun_randevular as $r): ?>
              <tr id="satir-<?= $r['id'] ?>">
                <td><strong><?= substr($r['baslangic_saati'],0,5) ?> – <?= substr($r['bitis_saati'],0,5) ?></strong></td>
                <td class="td-isim"><?= htmlspecialchars($r['musteri_ad'] . ' ' . $r['musteri_soyad']) ?></td>
                <td class="td-tel"><?= htmlspecialchars($r['musteri_telefon']) ?></td>
                <td><?= htmlspecialchars($r['hizmet_ad']) ?></td>
                <td><span class="badge badge-<?= $r['durum'] ?>"><?= ucfirst($r['durum']) ?></span></td>
                <td>
                  <div class="action-btns">
                    <?php if ($r['durum'] !== 'tamamlandi'): ?>
                    <button class="btn-action btn-tamamla" onclick="durumGuncelle(<?= $r['id'] ?>,'tamamlandi')">✓ Tamamla</button>
                    <?php endif; ?>
                    <?php if ($r['durum'] !== 'iptal'): ?>
                    <button class="btn-action btn-iptal" onclick="durumGuncelle(<?= $r['id'] ?>,'iptal')">✕ İptal</button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- Haftalık özet -->
      <?php if (!empty($haftalik)): ?>
      <div class="table-card">
        <div class="table-header"><h3>📊 Haftalık Özet</h3></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Tarih</th><th>Randevu Sayısı</th></tr></thead>
            <tbody>
              <?php foreach ($haftalik as $h): ?>
              <tr>
                <td><?= date('d.m.Y l', strtotime($h['tarih'])) ?></td>
                <td><strong><?= $h['sayi'] ?></strong></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99;" onclick="document.getElementById('sidebar').classList.remove('open');this.style.display='none';"></div>
<script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
