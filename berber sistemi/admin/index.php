<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

session_start();
$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici = trim($_POST['kullanici_adi'] ?? '');
    $sifre     = $_POST['sifre'] ?? '';

    if ($kullanici && $sifre) {
        $stmt = db()->prepare("SELECT id, sifre_hash, ad_soyad FROM admin WHERE kullanici_adi = ?");
        $stmt->execute([$kullanici]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($sifre, $admin['sifre_hash'])) {
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_ad_soyad'] = $admin['ad_soyad'];
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }
    }
    $hata = 'Kullanıcı adı veya şifre hatalı';
}

if (isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Giriş - AdonisBerber</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="login-page">
  <div class="login-logo-outer">
    <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Adonis Berber" class="login-logo-img">
    <h1>Adonis<span>Berber</span></h1>
    <p>Admin Paneli</p>
  </div>
  <div class="login-box">

    <?php if ($hata): ?>
    <div class="login-error">⚠ <?= htmlspecialchars($hata) ?></div>
    <?php endif; ?>

    <form class="login-form" method="POST">
      <div class="form-group">
        <label>Kullanıcı Adı</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input type="text" name="kullanici_adi" placeholder="" required autocomplete="username"
                 value="<?= htmlspecialchars($_POST['kullanici_adi'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Şifre</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input type="password" name="sifre" placeholder="••••••••" required autocomplete="current-password">
        </div>
      </div>
      <button type="submit" class="btn-login">Giriş Yap</button>
    </form>

  </div>
</div>
</body>
</html>
