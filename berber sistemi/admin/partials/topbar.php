<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
<div class="topbar">
  <div class="topbar-left">
    <h1><?= $topbar_baslik ?? 'Admin Panel' ?></h1>
    <p><?= $topbar_alt ?? date('d F Y') ?></p>
  </div>
  <div class="topbar-right">
    <button class="sidebar-toggle" id="sidebarToggle" onclick="document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sidebarOverlay').style.display = document.getElementById('sidebar').classList.contains('open') ? 'block' : 'none'">☰</button>
  </div>
</div>
