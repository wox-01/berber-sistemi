<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
admin_giris_kontrol();

$pdo = db();
$topbar_baslik = 'Çalışma Saatleri';
$topbar_alt    = 'Haftalık program & tatil günleri';

$gun_adlari = ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'];

$saatler = $pdo->query("SELECT * FROM calisma_saatleri ORDER BY gun")->fetchAll(PDO::FETCH_UNIQUE);
$tatiller = $pdo->query("SELECT * FROM tatil_gunleri ORDER BY tarih DESC LIMIT 30")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Çalışma Saatleri - Admin</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <div class="main-content">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <div class="page-content">

      <!-- Çalışma saatleri formu -->
      <div class="form-card" style="margin-bottom:1.5rem;">
        <div class="form-card-header">
          <h3>🕐 Haftalık Çalışma Programı</h3>
        </div>
        <form action="<?= BASE_URL ?>/api/admin_saat.php" method="POST" data-ajax="guncelle">
          <input type="hidden" name="action" value="guncelle">
          <div style="border-radius:8px;overflow:hidden;border:1px solid var(--border);">
            <?php for ($g = 0; $g <= 6; $g++):
              $s = $saatler[$g] ?? ['aktif'=>0,'acilis'=>'09:00','kapanis'=>'19:00'];
            ?>
            <div class="gun-satir <?= $s['aktif'] ? '' : 'kapali' ?>" id="gun-satir-<?= $g ?>">
              <div class="gun-adi"><?= $gun_adlari[$g] ?></div>
              <div class="toggle-wrap">
                <label class="toggle">
                  <input type="checkbox" name="aktif_<?= $g ?>" id="aktif_<?= $g ?>"
                         <?= $s['aktif'] ? 'checked' : '' ?>
                         onchange="gunToggle(<?= $g ?>)">
                  <span class="toggle-slider"></span>
                </label>
                <span class="toggle-label"><?= $s['aktif'] ? 'Açık' : 'Kapalı' ?></span>
              </div>
              <div class="form-group">
                <label>Açılış</label>
                <input type="time" name="acilis_<?= $g ?>" class="form-control"
                       value="<?= substr($s['acilis'],0,5) ?>"
                       <?= $s['aktif'] ? '' : 'disabled' ?>>
              </div>
              <div class="form-group">
                <label>Kapanış</label>
                <input type="time" name="kapanis_<?= $g ?>" class="form-control"
                       value="<?= substr($s['kapanis'],0,5) ?>"
                       <?= $s['aktif'] ? '' : 'disabled' ?>>
              </div>
            </div>
            <?php endfor; ?>
          </div>
          <div style="margin-top:1.2rem;text-align:right;">
            <button type="submit" class="btn btn-primary">💾 Kaydet</button>
          </div>
        </form>
      </div>

      <!-- Tatil günleri -->
      <div class="form-card">
        <div class="form-card-header">
          <h3>🚫 Tatil / Kapalı Günler</h3>
        </div>

        <form action="<?= BASE_URL ?>/api/admin_saat.php" method="POST" data-ajax="tatil_ekle" style="margin-bottom:1.5rem;">
          <input type="hidden" name="action" value="tatil_ekle">
          <div class="form-grid col2" style="align-items:flex-end;">
            <div class="form-group">
              <label>Tarih *</label>
              <input type="date" name="tarih" class="form-control" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
              <label>Açıklama</label>
              <input type="text" name="aciklama" class="form-control" placeholder="Resmi tatil, özel gün...">
            </div>
          </div>
          <div style="margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Tatil Ekle</button>
          </div>
        </form>

        <?php if (!empty($tatiller)): ?>
        <table>
          <thead><tr><th>Tarih</th><th>Açıklama</th><th>İşlem</th></tr></thead>
          <tbody>
            <?php foreach ($tatiller as $t): ?>
            <tr id="tatil-<?= $t['id'] ?>">
              <td><strong><?= date('d.m.Y', strtotime($t['tarih'])) ?></strong></td>
              <td><?= htmlspecialchars($t['aciklama'] ?? '—') ?></td>
              <td>
                <button class="btn-action btn-sil" onclick="tatilSil(<?= $t['id'] ?>)">🗑 Kaldır</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div style="color:var(--text-muted);font-size:0.9rem;padding:1rem 0;">Tatil günü eklenmemiş</div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99;"></div>
<script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
<script>
function gunToggle(gun) {
  const satir = document.getElementById('gun-satir-' + gun);
  const aktif = document.getElementById('aktif_' + gun).checked;
  satir.classList.toggle('kapali', !aktif);
  satir.querySelectorAll('input[type=time]').forEach(el => el.disabled = !aktif);
  satir.querySelector('.toggle-label').textContent = aktif ? 'Açık' : 'Kapalı';
}

async function tatilSil(id) {
  if (!confirm('Bu tatil gününü kaldırmak istediğinizden emin misiniz?')) return;
  const fd = new FormData();
  fd.append('action', 'tatil_sil');
  fd.append('id', id);
  const res  = await fetch((window.BASE_URL||'') + '/api/admin_saat.php', { method:'POST', body:fd });
  const data = await res.json();
  if (data.basari) { toast('Tatil günü kaldırıldı', 'success'); document.getElementById('tatil-'+id)?.remove(); }
  else             { toast(data.hata || 'Hata', 'error'); }
}
</script>
</body>
</html>
