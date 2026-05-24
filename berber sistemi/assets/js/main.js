// ========================
// BERBER SİSTEMİ - MAIN JS
// ========================

// --- Toast ---
function toast(mesaj, tip = 'info', sure = 3500) {
  const c = document.getElementById('toastContainer');
  if (!c) return;
  const t = document.createElement('div');
  const ikonlar = { success: '✓', error: '✕', info: 'ℹ' };
  t.className = `toast ${tip}`;
  t.innerHTML = `<span>${ikonlar[tip] || 'ℹ'}</span><span>${mesaj}</span>`;
  c.appendChild(t);
  setTimeout(() => t.remove(), sure);
}

// --- Hamburger + Navbar scroll + Scroll reveal ---
document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.getElementById('hamburger');
  const navMenu   = document.getElementById('navMenu');
  if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
      navMenu.classList.toggle('open');
      hamburger.classList.toggle('open');
    });
    navMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      navMenu.classList.remove('open');
      hamburger.classList.remove('open');
    }));
  }

  // Navbar scroll shrink
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });
  }

  // Scroll reveal
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
});

// ========================
// RANDEVU FORMU
// ========================
(function() {
  const formEl = document.getElementById('randevuForm');
  if (!formEl) return;

  let secim = { hizmet_id: null, hizmet_ad: '', hizmet_sure: 0, hizmet_fiyat: 0, tarih: '', saat: '' };
  let takvimYil, takvimAy;

  const gunler    = ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'];
  const aylar     = ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
  const bugun     = new Date();
  takvimYil = bugun.getFullYear();
  takvimAy  = bugun.getMonth();

  // --- ADIMLAR ---
  function gosterAdim(n) {
    document.querySelectorAll('.adim').forEach((el, i) => {
      el.classList.toggle('aktif', i + 1 === n);
    });
    document.querySelectorAll('.progress-step').forEach((el, i) => {
      el.classList.remove('active', 'done');
      if (i + 1 === n) el.classList.add('active');
      if (i + 1 < n)  el.classList.add('done');
    });
    document.querySelectorAll('.progress-line').forEach((el, i) => {
      el.classList.toggle('done', i + 1 < n);
    });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // --- HİZMET SEÇİMİ ---
  document.querySelectorAll('.hizmet-secim-item').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.hizmet-secim-item').forEach(i => i.classList.remove('selected'));
      item.classList.add('selected');
      secim.hizmet_id    = +item.dataset.id;
      secim.hizmet_ad    = item.dataset.ad;
      secim.hizmet_sure  = +item.dataset.sure;
      secim.hizmet_fiyat = +item.dataset.fiyat;
    });
  });

  document.getElementById('btnAdim1')?.addEventListener('click', () => {
    if (!secim.hizmet_id) { toast('Lütfen bir hizmet seçin', 'error'); return; }
    gosterAdim(2);
    takvimCiz(takvimYil, takvimAy);
  });

  // --- TAKVİM ---
  function takvimCiz(yil, ay) {
    const baslik   = document.getElementById('takvimBaslik');
    const gunContainer = document.getElementById('takvimGunler');
    if (!baslik || !gunContainer) return;

    baslik.textContent = `${aylar[ay]} ${yil}`;
    gunContainer.innerHTML = '';

    gunler.forEach(g => {
      const d = document.createElement('div');
      d.className = 'takvim-gun-baslik';
      d.textContent = g;
      gunContainer.appendChild(d);
    });

    const ilkGun  = new Date(yil, ay, 1).getDay();
    const sonGun  = new Date(yil, ay + 1, 0).getDate();
    const bugunTs = new Date(bugun.getFullYear(), bugun.getMonth(), bugun.getDate());

    for (let i = 0; i < ilkGun; i++) {
      const d = document.createElement('div');
      d.className = 'takvim-gun bos';
      gunContainer.appendChild(d);
    }

    for (let g = 1; g <= sonGun; g++) {
      const tarih = new Date(yil, ay, g);
      const d = document.createElement('div');
      d.className = 'takvim-gun';
      d.textContent = g;

      const tarihStr = `${yil}-${String(ay+1).padStart(2,'0')}-${String(g).padStart(2,'0')}`;

      if (tarih < bugunTs) {
        d.classList.add('disabled');
      } else {
        if (tarih.getTime() === bugunTs.getTime()) d.classList.add('today');
        if (secim.tarih === tarihStr) d.classList.add('selected');

        d.addEventListener('click', () => {
          document.querySelectorAll('.takvim-gun.selected').forEach(el => el.classList.remove('selected'));
          d.classList.add('selected');
          secim.tarih = tarihStr;
          secim.saat  = '';
          slotlariYukle(tarihStr);
        });
      }

      gunContainer.appendChild(d);
    }
  }

  document.getElementById('takvimGeri')?.addEventListener('click', () => {
    takvimAy--;
    if (takvimAy < 0) { takvimAy = 11; takvimYil--; }
    takvimCiz(takvimYil, takvimAy);
  });

  document.getElementById('takvimIleri')?.addEventListener('click', () => {
    takvimAy++;
    if (takvimAy > 11) { takvimAy = 0; takvimYil++; }
    takvimCiz(takvimYil, takvimAy);
  });

  // --- SLOT YÜKLEMESİ ---
  async function slotlariYukle(tarih) {
    const grid = document.getElementById('saatGrid');
    if (!grid) return;
    grid.innerHTML = '<div class="saat-yuklenıyor"><div class="spinner"></div> Müsait saatler yükleniyor...</div>';
    secim.saat = '';

    try {
      const res  = await fetch(`${window.BASE_URL || ''}/api/get_slots.php?tarih=${tarih}&hizmet_id=${secim.hizmet_id}`);
      const data = await res.json();

      if (data.hata) { grid.innerHTML = `<div class="saat-bos">${data.hata}</div>`; return; }
      if (!data.slotlar.length) { grid.innerHTML = '<div class="saat-bos">Bu tarihte müsait saat yok</div>'; return; }

      grid.innerHTML = '';
      data.slotlar.forEach(saat => {
        const s = document.createElement('div');
        s.className = 'saat-slot';
        s.textContent = saat;
        s.addEventListener('click', () => {
          document.querySelectorAll('.saat-slot.selected').forEach(el => el.classList.remove('selected'));
          s.classList.add('selected');
          secim.saat = saat;
        });
        grid.appendChild(s);
      });
    } catch(e) {
      grid.innerHTML = '<div class="saat-bos">Saatler yüklenirken hata oluştu</div>';
    }
  }

  document.getElementById('btnAdim2')?.addEventListener('click', () => {
    if (!secim.tarih) { toast('Lütfen tarih seçin', 'error'); return; }
    if (!secim.saat)  { toast('Lütfen saat seçin', 'error'); return; }
    gosterAdim(3);
  });

  document.getElementById('btnAdim2Geri')?.addEventListener('click', () => gosterAdim(1));
  document.getElementById('btnAdim3Geri')?.addEventListener('click', () => gosterAdim(2));

  // --- FORM GÖNDER ---
  formEl.addEventListener('submit', async (e) => {
    e.preventDefault();

    const ad      = document.getElementById('musteriAd')?.value.trim();
    const soyad   = document.getElementById('musteriSoyad')?.value.trim();
    const telefon = document.getElementById('musteriTel')?.value.trim();
    const notlar  = document.getElementById('musteriNotlar')?.value.trim();

    if (!ad || !soyad || !telefon) { toast('Ad, soyad ve telefon zorunlu', 'error'); return; }

    const btnGonder = document.getElementById('btnGonder');
    btnGonder.disabled = true;
    btnGonder.innerHTML = '<span class="spinner"></span> Randevu alınıyor...';

    try {
      const res  = await fetch(`${window.BASE_URL || ''}/api/create_randevu.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ad, soyad, telefon, hizmet_id: secim.hizmet_id, tarih: secim.tarih, saat: secim.saat, notlar })
      });
      const data = await res.json();

      if (data.basari) {
        document.getElementById('basariHizmet').textContent  = secim.hizmet_ad;
        document.getElementById('basariTarih').textContent   = secim.tarih;
        document.getElementById('basariSaat').textContent    = secim.saat;
        document.getElementById('basariMusteri').textContent = `${ad} ${soyad}`;
        gosterAdim(4);
      } else {
        toast(data.hata || 'Randevu alınamadı', 'error');
      }
    } catch(e) {
      toast('Bağlantı hatası', 'error');
    } finally {
      btnGonder.disabled = false;
      btnGonder.innerHTML = 'Randevu Al';
    }
  });

})();
