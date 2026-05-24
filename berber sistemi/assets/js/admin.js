// =========================
// BERBER SİSTEMİ - ADMİN JS
// =========================

// --- Toast ---
function toast(mesaj, tip = 'info', sure = 3500) {
  const c = document.getElementById('toastContainer');
  if (!c) return;
  const t = document.createElement('div');
  t.className = `toast ${tip}`;
  t.innerHTML = `<span>${tip === 'success' ? '✓' : '✕'}</span><span>${mesaj}</span>`;
  c.appendChild(t);
  setTimeout(() => t.remove(), sure);
}

// --- Sidebar toggle (mobil) ---
document.addEventListener('DOMContentLoaded', () => {
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (toggle && sidebar) {
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('visible');
    });
    overlay?.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('visible');
    });
  }

  // Modal kapat
  document.querySelectorAll('.modal-close, [data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('open'));
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
  });
});

function modalAc(id) {
  document.getElementById(id)?.classList.add('open');
}
function modalKapat(id) {
  document.getElementById(id)?.classList.remove('open');
}

// --- Randevu durum güncelle ---
async function durumGuncelle(id, durum) {
  const fd = new FormData();
  fd.append('action', 'durum_guncelle');
  fd.append('id', id);
  fd.append('durum', durum);

  try {
    const res  = await fetch((window.BASE_URL||'') + '/api/admin_randevu.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.basari) { toast('Durum güncellendi', 'success'); setTimeout(() => location.reload(), 800); }
    else             { toast(data.hata || 'Hata', 'error'); }
  } catch(e) { toast('Bağlantı hatası', 'error'); }
}

async function randevuSil(id) {
  if (!confirm('Bu randevuyu silmek istediğinizden emin misiniz?')) return;
  const fd = new FormData();
  fd.append('action', 'sil');
  fd.append('id', id);

  try {
    const res  = await fetch((window.BASE_URL||'') + '/api/admin_randevu.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.basari) { toast('Randevu silindi', 'success'); document.getElementById(`satir-${id}`)?.remove(); }
    else             { toast(data.hata || 'Hata', 'error'); }
  } catch(e) { toast('Bağlantı hatası', 'error'); }
}

// --- Hizmet işlemleri ---
function hizmetDuzenle(id, ad, sure, fiyat, sira) {
  document.getElementById('editHizmetId').value    = id;
  document.getElementById('editHizmetAd').value    = ad;
  document.getElementById('editHizmetSure').value  = sure;
  document.getElementById('editHizmetFiyat').value = fiyat;
  document.getElementById('editHizmetSira').value  = sira;
  modalAc('modalHizmetDuzenle');
}

async function hizmetToggle(id) {
  const fd = new FormData();
  fd.append('action', 'aktif_toggle');
  fd.append('id', id);
  try {
    const res  = await fetch((window.BASE_URL||'') + '/api/admin_hizmet.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.basari) { toast('Güncellendi', 'success'); setTimeout(() => location.reload(), 600); }
    else             { toast(data.hata || 'Hata', 'error'); }
  } catch(e) { toast('Bağlantı hatası', 'error'); }
}

async function hizmetSil(id) {
  if (!confirm('Bu hizmeti silmek istediğinizden emin misiniz?')) return;
  const fd = new FormData();
  fd.append('action', 'sil');
  fd.append('id', id);
  try {
    const res  = await fetch((window.BASE_URL||'') + '/api/admin_hizmet.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.basari) { toast('Hizmet silindi', 'success'); document.getElementById(`hizmet-${id}`)?.remove(); }
    else             { toast(data.hata || 'Hata', 'error'); }
  } catch(e) { toast('Bağlantı hatası', 'error'); }
}

// --- Saat satırı toggle ---
function gunToggle(gun) {
  const satir = document.getElementById(`gun-satir-${gun}`);
  const aktif = document.getElementById(`aktif_${gun}`).checked;
  satir?.classList.toggle('kapali', !aktif);
}

// --- Filtre ---
function tabloFiltrele() {
  const arama = document.getElementById('aramaInput')?.value.toLowerCase() || '';
  document.querySelectorAll('tbody tr[data-arama]').forEach(tr => {
    tr.style.display = tr.dataset.arama.toLowerCase().includes(arama) ? '' : 'none';
  });
}

// Form gönderimlerini Ajax'a çevir (admin formları)
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-ajax]').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const action = form.dataset.ajax;
      const btn    = form.querySelector('[type=submit]');
      const oText  = btn?.textContent;
      if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>'; }

      try {
        const res  = await fetch(form.getAttribute('action'), { method: 'POST', body: new FormData(form) });
        const data = await res.json();
        if (data.basari) { toast('Kaydedildi', 'success'); setTimeout(() => location.reload(), 800); }
        else             { toast(data.hata || 'Hata oluştu', 'error'); }
      } catch(e) { toast('Bağlantı hatası', 'error'); }
      finally { if (btn) { btn.disabled = false; btn.textContent = oText; } }
    });
  });
});
