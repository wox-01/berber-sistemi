<?php
$current = basename($_SERVER['PHP_SELF']);
function nav_aktif(string $dosya): string {
    global $current;
    return $current === $dosya ? 'active' : '';
}
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">✂️</div>
    <div>
      <h2>Adonis<span>Berber</span></h2>
      <small>Admin Panel</small>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Genel</div>
    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= nav_aktif('dashboard.php') ?>">
      <span class="nav-icon">📊</span> Dashboard
    </a>

    <div class="nav-section">Yönetim</div>
    <a href="<?= BASE_URL ?>/admin/randevular.php" class="<?= nav_aktif('randevular.php') ?>">
      <span class="nav-icon">📅</span> Randevular
    </a>
    <a href="<?= BASE_URL ?>/admin/musteriler.php" class="<?= nav_aktif('musteriler.php') ?>">
      <span class="nav-icon">👥</span> Müşteriler
    </a>
    <a href="<?= BASE_URL ?>/admin/hizmetler.php" class="<?= nav_aktif('hizmetler.php') ?>">
      <span class="nav-icon">✂️</span> Hizmetler
    </a>
    <a href="<?= BASE_URL ?>/admin/saatler.php" class="<?= nav_aktif('saatler.php') ?>">
      <span class="nav-icon">🕐</span> Çalışma Saatleri
    </a>

    <div class="nav-section">Site</div>
    <a href="<?= BASE_URL ?>/" target="_blank">
      <span class="nav-icon">🌐</span> Siteyi Gör
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-info">
      <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_ad_soyad'] ?? 'A', 0, 1)) ?></div>
      <div class="admin-info-text">
        <h4><?= htmlspecialchars($_SESSION['admin_ad_soyad'] ?? 'Admin') ?></h4>
        <small>Yönetici</small>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/admin/logout.php" class="btn-cikis">🚪 Çıkış Yap</a>
  </div>
</aside>
