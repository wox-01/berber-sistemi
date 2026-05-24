<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
admin_giris_kontrol();

$pdo = db();
$topbar_baslik = 'Hizmetler';
$topbar_alt    = 'Hizmet & fiyat yönetimi';

$hizmetler = $pdo->query("SELECT * FROM hizmetler ORDER BY sira, id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hizmetler - Admin</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <div class="main-content">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <div class="page-content">

      <!-- Yeni hizmet ekle -->
      <div class="form-card" style="margin-bottom:1.5rem;">
        <div class="form-card-header">
          <h3>➕ Yeni Hizmet Ekle</h3>
        </div>
        <form action="<?= BASE_URL ?>/api/admin_hizmet.php" method="POST" data-ajax="ekle">
          <input type="hidden" name="action" value="ekle">
          <div class="form-grid col3">
            <div class="form-group">
              <label>Hizmet Adı *</label>
              <input type="text" name="ad" class="form-control" placeholder="Saç Kesimi" required>
            </div>
            <div class="form-group">
              <label>Süre (dakika) *</label>
              <input type="number" name="sure_dakika" class="form-control" value="30" min="5" max="240" required>
            </div>
            <div class="form-group">
              <label>Fiyat (₺) *</label>
              <input type="number" name="fiyat" class="form-control" value="0" min="0" step="5" required>
            </div>
          </div>
          <div style="margin-top:1rem;text-align:right;">
            <button type="submit" class="btn btn-primary">Ekle</button>
          </div>
        </form>
      </div>

      <!-- Hizmet listesi -->
      <div class="table-card">
        <div class="table-header"><h3>Mevcut Hizmetler (<?= count($hizmetler) ?>)</h3></div>
        <div class="table-wrap">
          <?php if (empty($hizmetler)): ?>
          <div class="table-empty"><div class="empty-icon">✂️</div><p>Henüz hizmet eklenmedi</p></div>
          <?php else: ?>
          <table>
            <thead>
              <tr><th>Hizmet Adı</th><th>Süre</th><th>Fiyat</th><th>Durum</th><th>İşlem</th></tr>
            </thead>
            <tbody>
              <?php foreach ($hizmetler as $h): ?>
              <tr id="hizmet-<?= $h['id'] ?>">
                <td class="td-isim"><?= htmlspecialchars($h['ad']) ?></td>
                <td>⏱ <?= $h['sure_dakika'] ?> dk</td>
                <td style="color:var(--gold);font-weight:700;"><?= number_format($h['fiyat'],0,',','.') ?> ₺</td>
                <td>
                  <span class="badge <?= $h['aktif'] ? 'badge-onaylandi' : 'badge-iptal' ?>">
                    <?= $h['aktif'] ? 'Aktif' : 'Pasif' ?>
                  </span>
                </td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action btn-duzenle"
                            onclick="hizmetDuzenle(<?= $h['id'] ?>,'<?= htmlspecialchars(addslashes($h['ad'])) ?>',<?= $h['sure_dakika'] ?>,<?= $h['fiyat'] ?>,<?= $h['sira'] ?>)">
                      ✏ Düzenle
                    </button>
                    <button class="btn-action <?= $h['aktif'] ? 'btn-iptal' : 'btn-onayla' ?>"
                            onclick="hizmetToggle(<?= $h['id'] ?>)">
                      <?= $h['aktif'] ? 'Pasif Yap' : 'Aktif Yap' ?>
                    </button>
                    <button class="btn-action btn-sil" onclick="hizmetSil(<?= $h['id'] ?>)">🗑</button>
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

<!-- Modal: Hizmet Düzenle -->
<div class="modal-overlay" id="modalHizmetDuzenle">
  <div class="modal">
    <div class="modal-header">
      <h3>✏ Hizmet Düzenle</h3>
      <button class="modal-close">✕</button>
    </div>
    <form action="<?= BASE_URL ?>/api/admin_hizmet.php" method="POST" data-ajax="guncelle">
      <input type="hidden" name="action" value="guncelle">
      <input type="hidden" name="id" id="editHizmetId">
      <div class="form-grid">
        <div class="form-group">
          <label>Hizmet Adı *</label>
          <input type="text" name="ad" id="editHizmetAd" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Süre (dakika) *</label>
          <input type="number" name="sure_dakika" id="editHizmetSure" class="form-control" min="5" required>
        </div>
        <div class="form-group">
          <label>Fiyat (₺) *</label>
          <input type="number" name="fiyat" id="editHizmetFiyat" class="form-control" min="0" step="5">
        </div>
        <div class="form-group">
          <label>Sıra</label>
          <input type="number" name="sira" id="editHizmetSira" class="form-control" min="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary modal-close">İptal</button>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99;"></div>
<script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
