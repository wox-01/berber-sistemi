<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$hizmetler = aktif_hizmetleri_getir();

// --- İşletme bilgileri (buradan güncelle) ---
$berber_adres    = 'Adonis Erkek Kuaförü, Trabzon';
$berber_harita   = 'https://www.google.com/maps/place/Adonis+Erkek+Kuaf%C3%B6r%C3%BC/@41.1795531,41.817499,18z';
$berber_telefon  = '0(216) 000 00 00';
$berber_instagram = 'https://instagram.com/adonisberber';
$berber_facebook  = 'https://facebook.com/adonisberber';
$berber_twitter   = 'https://twitter.com/adonisberber';

// --- Açık/Kapalı durumu ---
$gun_no      = (int) date('w');
$saat_simdi  = date('H:i:s');
$stmt = db()->prepare("SELECT acilis, kapanis, aktif FROM calisma_saatleri WHERE gun = ?");
$stmt->execute([$gun_no]);
$calisma_bugun = $stmt->fetch();
$acik = $calisma_bugun
    && $calisma_bugun['aktif']
    && $saat_simdi >= $calisma_bugun['acilis']
    && $saat_simdi <  $calisma_bugun['kapanis'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adonis Berber - Modern Erkek Bakımı</title>
  <meta name="description" content="Online randevu alın, kaliteli berber hizmetinin keyfini çıkarın.">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a class="navbar-logo" href="<?= BASE_URL ?>/">
    <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Adonis Berber" class="navbar-logo-img">
    <span class="navbar-logo-text">Adonis<span class="navbar-logo-accent">Berber</span></span>
  </a>
  <ul class="navbar-nav" id="navMenu">
    <li><a href="#hizmetler">Hizmetler</a></li>
    <li><a href="#nasil-calisir">Nasıl Çalışır</a></li>
    <li><a href="<?= BASE_URL ?>/randevu-al.php" class="btn-randevu-nav">Randevu Al</a></li>
  </ul>
  <div class="hamburger" id="hamburger">
    <span></span><span></span><span></span>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <video class="hero-video" autoplay muted loop playsinline>
    <source src="https://videos.pexels.com/video-files/6113143/6113143-hd_1920_1080_30fps.mp4" type="video/mp4">
  </video>
  <div class="hero-content">
    <div class="hero-badge">Premium Berber Hizmeti</div>
    <h1>Stilinizi <span class="accent">Mükemmelleştirin</span></h1>
    <p>Deneyimli berberlerimizle saç kesimi, sakal bakımı ve daha fazlası. Online randevu ile bekleme yok.</p>
    <div class="hero-buttons">
      <a href="<?= BASE_URL ?>/randevu-al.php" class="btn btn-primary">Hemen Randevu Al</a>
      <a href="#hizmetler" class="btn btn-outline">Hizmetleri Gör</a>
    </div>
  </div>
</section>

<!-- HİZMETLER -->
<section class="section" id="hizmetler">
  <div class="container">
    <div class="hizmetler-layout">
      <div class="hizmetler-baslik reveal">
        <div class="section-tag">Hizmetlerimiz</div>
        <h2>Size<br>Özel<br>Bakım</h2>
        <a href="<?= BASE_URL ?>/randevu-al.php" class="btn btn-primary" style="margin-top:2.5rem;">Randevu Al</a>
      </div>
      <div class="hizmetler-liste">
        <?php foreach ($hizmetler as $h): ?>
        <div class="hizmet-satir reveal">
          <h3><?= htmlspecialchars($h['ad']) ?></h3>
          <?php if (!empty($h['aciklama'])): ?>
          <p><?= htmlspecialchars($h['aciklama']) ?></p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- NASIL ÇALIŞIR -->
<section class="section section-alt" id="nasil-calisir">
  <div class="container">
    <div class="section-header reveal">
      <div class="section-tag">Kolay & Hızlı</div>
      <h2>Nasıl Çalışır?</h2>
      <p>3 adımda randevunuzu alın</p>
    </div>
    <div class="adimlar-grid">
      <div class="adim-kart reveal">
        <div class="adim-numara">1</div>
        <h3>Hizmet Seçin</h3>
        <p>İstediğiniz hizmeti ve fiyatını görün</p>
      </div>
      <div class="adim-kart reveal">
        <div class="adim-numara">2</div>
        <h3>Tarih & Saat</h3>
        <p>Müsait günlerden uygun saati seçin</p>
      </div>
      <div class="adim-kart reveal">
        <div class="adim-numara">3</div>
        <h3>Onay Alın</h3>
        <p>Randevunuz anında onaylanır</p>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">

    <!-- Marka + Sosyal Medya -->
    <div class="footer-col">
      <div class="footer-logo">Adonis<span>Berber</span></div>
      <p class="footer-tagline">Modern erkek bakımı için doğru adres.</p>
      <div class="footer-sosyal">
        <a href="javascript:void(0)" class="sosyal-btn" title="Instagram">
          <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
        <a href="javascript:void(0)" class="sosyal-btn" title="Facebook">
          <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>
        <a href="javascript:void(0)" class="sosyal-btn" title="X / Twitter">
          <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
      </div>
    </div>

    <!-- Hızlı Linkler -->
    <div class="footer-col">
      <h4>Hızlı Linkler</h4>
      <a href="#hizmetler">Hizmetler</a>
      <a href="#nasil-calisir">Nasıl Çalışır?</a>
      <a href="<?= BASE_URL ?>/randevu-al.php">Randevu Al</a>
    </div>

    <!-- Konum & Durum (harita yok, sadece info) -->
    <div class="footer-col">
      <h4>Konum & Saatler</h4>
      <a class="footer-adres-link" href="<?= $berber_harita ?>" target="_blank" rel="noopener">
        <?= htmlspecialchars($berber_adres) ?>
      </a>
      <a class="footer-tel" href="tel:<?= preg_replace('/\D/','',$berber_telefon) ?>">
        <?= htmlspecialchars($berber_telefon) ?>
      </a>
      <div class="durum-badge <?= $acik ? 'acik' : 'kapali' ?>">
        <?php if ($acik): ?>
          Açık &nbsp;·&nbsp; <?= substr($calisma_bugun['acilis'],0,5) ?> – <?= substr($calisma_bugun['kapanis'],0,5) ?>
        <?php elseif ($calisma_bugun && $calisma_bugun['aktif']): ?>
          Kapalı &nbsp;·&nbsp; Açılış <?= substr($calisma_bugun['acilis'],0,5) ?>
        <?php else: ?>
          Bugün Kapalı
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- Tam genişlik harita -->
  <div class="footer-map-wrap">
    <iframe
      src="https://maps.google.com/maps?q=41.1795516,41.8184431&output=embed&hl=tr&z=17"
      width="100%" height="240" frameborder="0"
      allowfullscreen loading="lazy"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>

  <div class="footer-bottom">
    <div class="footer-copy">&copy; <?= date('Y') ?> AdonisBerber. Tüm hakları saklıdır.</div>
  </div>
</footer>

<div class="toast-container" id="toastContainer"></div>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
