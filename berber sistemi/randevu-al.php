<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$hizmetler = aktif_hizmetleri_getir();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Randevu Al - AdonisBerber</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>

<nav class="navbar">
  <div class="navbar-logo">
    <a href="<?= BASE_URL ?>/" class="navbar-logo">
      <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Adonis Berber" class="navbar-logo-img">
      <span class="navbar-logo-text">Adonis<span class="navbar-logo-accent">Berber</span></span>
    </a>
  </div>
  <ul class="navbar-nav" id="navMenu">
    <li><a href="<?= BASE_URL ?>/">Ana Sayfa</a></li>
    <li><a href="<?= BASE_URL ?>/#hizmetler">Hizmetler</a></li>
  </ul>
  <div class="hamburger" id="hamburger">
    <span></span><span></span><span></span>
  </div>
</nav>

<div class="randevu-page">
  <div class="randevu-container">

    <div style="text-align:center; margin-bottom:2.5rem;">
      <div class="section-tag">Online Randevu</div>
      <h1 style="font-size:2rem; margin-top:0.5rem;">Randevu <span style="color:var(--gold)">Al</span></h1>
    </div>

    <!-- Progress -->
    <div class="randevu-progress">
      <div class="progress-step active" id="step1">
        <div class="dot">1</div>
        <div class="label">Hizmet</div>
      </div>
      <div class="progress-line" id="line1"></div>
      <div class="progress-step" id="step2">
        <div class="dot">2</div>
        <div class="label">Tarih & Saat</div>
      </div>
      <div class="progress-line" id="line2"></div>
      <div class="progress-step" id="step3">
        <div class="dot">3</div>
        <div class="label">Bilgiler</div>
      </div>
      <div class="progress-line" id="line3"></div>
      <div class="progress-step" id="step4">
        <div class="dot">✓</div>
        <div class="label">Onay</div>
      </div>
    </div>

    <form id="randevuForm" novalidate>

      <!-- ADIM 1: Hizmet -->
      <div class="adim aktif" id="adim1">
        <div class="form-card">
          <h3>Hizmet Seçin</h3>
          <div class="hizmet-secim-grid">
            <?php foreach ($hizmetler as $i => $h): ?>
            <div class="hizmet-secim-item"
                 data-id="<?= $h['id'] ?>"
                 data-ad="<?= htmlspecialchars($h['ad']) ?>"
                 data-sure="<?= $h['sure_dakika'] ?>"
                 data-fiyat="<?= $h['fiyat'] ?>">
              <h4><?= htmlspecialchars($h['ad']) ?></h4>
              <div class="sure"><?= $h['sure_dakika'] ?> dk</div>
              <div class="fiyat"><?= number_format($h['fiyat'], 0, ',', '.') ?> ₺</div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-primary" id="btnAdim1">Devam Et →</button>
        </div>
      </div>

      <!-- ADIM 2: Tarih & Saat -->
      <div class="adim" id="adim2">
        <div class="form-card">
          <h3>Tarih Seçin</h3>
          <div class="takvim-wrapper">
            <div class="takvim-nav">
              <button type="button" class="takvim-nav-btn" id="takvimGeri">‹</button>
              <h4 id="takvimBaslik"></h4>
              <button type="button" class="takvim-nav-btn" id="takvimIleri">›</button>
            </div>
            <div class="takvim-gunler" id="takvimGunler"></div>
          </div>
        </div>
        <div class="form-card">
          <h3>Saat Seçin</h3>
          <div class="saat-grid" id="saatGrid">
            <div class="saat-bos">Önce tarih seçin</div>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" id="btnAdim2Geri">← Geri</button>
          <button type="button" class="btn btn-primary" id="btnAdim2">Devam Et →</button>
        </div>
      </div>

      <!-- ADIM 3: Kişisel Bilgiler + Özet -->
      <div class="adim" id="adim3">
        <div class="form-card">
          <h3>Bilgileriniz</h3>
          <div class="form-grid col2">
            <div class="form-group">
              <label>Ad *</label>
              <input type="text" id="musteriAd" class="form-control" placeholder="Adınız" required autocomplete="given-name">
            </div>
            <div class="form-group">
              <label>Soyad *</label>
              <input type="text" id="musteriSoyad" class="form-control" placeholder="Soyadınız" required autocomplete="family-name">
            </div>
          </div>
          <div class="form-grid" style="margin-top:1.2rem;">
            <div class="form-group">
              <label>Telefon *</label>
              <input type="tel" id="musteriTel" class="form-control" placeholder="0 (5XX) XXX XX XX" maxlength="17" required autocomplete="tel" inputmode="numeric">
            </div>
            <div class="form-group">
              <label>Not (opsiyonel)</label>
              <input type="text" id="musteriNotlar" class="form-control" placeholder="Özel istek varsa...">
            </div>
          </div>
        </div>
        <div class="form-card">
          <h3>Randevu Özeti</h3>
          <div class="ozet-satir">
            <span class="etiket">Hizmet</span>
            <span class="deger" id="ozetHizmet">—</span>
          </div>
          <div class="ozet-satir">
            <span class="etiket">Tarih</span>
            <span class="deger" id="ozetTarih">—</span>
          </div>
          <div class="ozet-satir">
            <span class="etiket">Saat</span>
            <span class="deger" id="ozetSaat">—</span>
          </div>
          <div class="ozet-satir">
            <span class="etiket">Ücret</span>
            <span class="deger gold" id="ozetFiyat">—</span>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" id="btnAdim3Geri">← Geri</button>
          <button type="submit" class="btn btn-primary" id="btnGonder">Randevu Al</button>
        </div>
      </div>

      <!-- ADIM 4: Başarı -->
      <div class="adim" id="adim4">
        <div class="form-card">
          <div class="basari-ekran">
            <div class="basari-icon">✓</div>
            <h2>Randevunuz Alındı!</h2>
            <p>Sizi bekliyoruz. Randevu bilgilerinizi not alın.</p>
            <div style="background:var(--bg-secondary);border:1px solid var(--border-gold);border-radius:10px;padding:1.5rem;margin:1.5rem 0;text-align:left;">
              <div class="ozet-satir"><span class="etiket">Müşteri</span><span class="deger" id="basariMusteri"></span></div>
              <div class="ozet-satir"><span class="etiket">Hizmet</span><span class="deger" id="basariHizmet"></span></div>
              <div class="ozet-satir"><span class="etiket">Tarih</span><span class="deger" id="basariTarih"></span></div>
              <div class="ozet-satir"><span class="etiket">Saat</span><span class="deger gold" id="basariSaat"></span></div>
            </div>
            <div class="basari-butonlar">
              <a href="<?= BASE_URL ?>/" class="btn btn-outline">Ana Sayfaya Dön</a>
              <a href="<?= BASE_URL ?>/randevu-al.php" class="btn btn-primary">Yeni Randevu</a>
            </div>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
// Telefon mask: 0 (5XX) XXX XX XX
(function() {
  const tel = document.getElementById('musteriTel');
  if (!tel) return;

  function fmt(raw) {
    let d = raw.replace(/\D/g, '').slice(0, 11);
    if (d.length > 0 && d[0] !== '0') d = '0' + d.slice(0, 10);
    let out = '';
    for (let i = 0; i < d.length; i++) {
      if (i === 1) out += ' (';
      else if (i === 4) out += ') ';
      else if (i === 7 || i === 9) out += ' ';
      out += d[i];
    }
    return out;
  }

  tel.addEventListener('input', function() {
    const start = this.selectionStart;
    const oldLen = this.value.length;
    this.value = fmt(this.value);
    const diff = this.value.length - oldLen;
    const newPos = Math.max(0, start + diff);
    this.setSelectionRange(newPos, newPos);
  });

  tel.addEventListener('keydown', function(e) {
    if (e.key !== 'Backspace' || this.selectionStart !== this.selectionEnd) return;
    const maskAt = [2, 3, 7, 8, 12, 15]; // positions of mask chars: ' (', ') ', ' ', ' '
    const pos = this.selectionStart;
    if (maskAt.includes(pos - 1)) {
      e.preventDefault();
      const raw = this.value.replace(/\D/g, '').slice(0, -1);
      this.value = fmt(raw);
      const newPos = pos - 2;
      this.setSelectionRange(newPos, newPos);
    }
  });
})();

document.getElementById('btnAdim2')?.addEventListener('click', function() {
  setTimeout(() => {
    const items  = document.querySelectorAll('.hizmet-secim-item.selected');
    const saatEl = document.querySelector('.saat-slot.selected');
    if (!items.length) return;
    const item = items[0];
    document.getElementById('ozetHizmet').textContent = item.dataset.ad;
    document.getElementById('ozetTarih').textContent  = (() => {
      const navEl = document.getElementById('takvimBaslik');
      return navEl ? navEl.textContent : '—';
    })();
    document.getElementById('ozetSaat').textContent   = saatEl?.textContent || '—';
    document.getElementById('ozetFiyat').textContent  = Number(item.dataset.fiyat).toLocaleString('tr-TR') + ' ₺';
  }, 100);
}, true);
</script>
</body>
</html>
