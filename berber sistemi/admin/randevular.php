<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
admin_giris_kontrol();

$pdo = db();
$topbar_baslik = 'Randevular';
$topbar_alt    = 'Tüm randevular';

$where  = ['1=1'];
$params = [];

$filtre_tarih = $_GET['tarih'] ?? '';
$filtre_durum = $_GET['durum'] ?? '';
$filtre_arama = $_GET['arama'] ?? '';

if ($filtre_tarih) { $where[] = 'r.tarih = ?'; $params[] = $filtre_tarih; }
if ($filtre_durum) { $where[] = 'r.durum = ?'; $params[] = $filtre_durum; }
if ($filtre_arama) {
    $where[]  = '(r.musteri_ad LIKE ? OR r.musteri_soyad LIKE ? OR r.musteri_telefon LIKE ?)';
    $like = "%{$filtre_arama}%";
    $params = array_merge($params, [$like,$like,$like]);
}

$sql  = "SELECT r.*, h.ad AS hizmet_ad, h.fiyat FROM randevular r JOIN hizmetler h ON r.hizmet_id = h.id WHERE " . implode(' AND ', $where) . " ORDER BY r.tarih DESC, r.baslangic_saati ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$randevular = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Randevular - Admin</title>
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
          <h3>Tüm Randevular (<?= count($randevular) ?>)</h3>
          <div class="table-filters">
            <form method="GET" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
              <input type="text" name="arama" class="filter-input" placeholder="İsim, telefon..." value="<?= htmlspecialchars($filtre_arama) ?>">
              <input type="date" name="tarih" class="filter-input" value="<?= htmlspecialchars($filtre_tarih) ?>">
              <select name="durum" class="filter-input">
                <option value="">Tüm Durumlar</option>
                <option value="onaylandi"  <?= $filtre_durum==='onaylandi'?'selected':'' ?>>Onaylı</option>
                <option value="beklemede"  <?= $filtre_durum==='beklemede'?'selected':'' ?>>Bekleyen</option>
                <option value="tamamlandi" <?= $filtre_durum==='tamamlandi'?'selected':'' ?>>Tamamlandı</option>
                <option value="iptal"      <?= $filtre_durum==='iptal'?'selected':'' ?>>İptal</option>
              </select>
              <button type="submit" class="btn btn-primary" style="padding:0.45rem 1rem;font-size:0.85rem;">Filtrele</button>
              <?php if ($filtre_tarih || $filtre_durum || $filtre_arama): ?>
              <a href="<?= BASE_URL ?>/admin/randevular.php" class="btn btn-secondary" style="padding:0.45rem 0.8rem;font-size:0.85rem;">✕</a>
              <?php endif; ?>
            </form>
          </div>
        </div>
        <div class="table-wrap">
          <?php if (empty($randevular)): ?>
          <div class="table-empty">
            <div class="empty-icon">📭</div>
            <p>Randevu bulunamadı</p>
          </div>
          <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Tarih & Saat</th>
                <th>Müşteri</th>
                <th>Telefon</th>
                <th>Hizmet</th>
                <th>Ücret</th>
                <th>Durum</th>
                <th>İşlem</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($randevular as $r): ?>
              <tr id="satir-<?= $r['id'] ?>">
                <td style="color:var(--text-muted);font-size:0.8rem;">#<?= $r['id'] ?></td>
                <td>
                  <strong><?= date('d.m.Y', strtotime($r['tarih'])) ?></strong><br>
                  <span style="color:var(--gold);font-size:0.82rem;"><?= substr($r['baslangic_saati'],0,5) ?> – <?= substr($r['bitis_saati'],0,5) ?></span>
                </td>
                <td class="td-isim"><?= htmlspecialchars($r['musteri_ad'] . ' ' . $r['musteri_soyad']) ?></td>
                <td class="td-tel"><?= htmlspecialchars($r['musteri_telefon']) ?></td>
                <td><?= htmlspecialchars($r['hizmet_ad']) ?></td>
                <td style="color:var(--gold);"><?= number_format($r['fiyat'],0,',','.') ?> ₺</td>
                <td><span class="badge badge-<?= $r['durum'] ?>"><?= ucfirst($r['durum']) ?></span></td>
                <td>
                  <div class="action-btns">
                    <?php if ($r['durum'] === 'beklemede'): ?>
                    <button class="btn-action btn-onayla"  onclick="durumGuncelle(<?= $r['id'] ?>,'onaylandi')">✓ Onayla</button>
                    <?php endif; ?>
                    <?php if (!in_array($r['durum'], ['tamamlandi','iptal'])): ?>
                    <button class="btn-action btn-tamamla" onclick="durumGuncelle(<?= $r['id'] ?>,'tamamlandi')">✓ Tamamla</button>
                    <button class="btn-action btn-iptal"   onclick="durumGuncelle(<?= $r['id'] ?>,'iptal')">✕ İptal</button>
                    <?php endif; ?>
                    <button class="btn-action btn-sil" onclick="randevuSil(<?= $r['id'] ?>)">🗑</button>
                  </div>
                </td>
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
