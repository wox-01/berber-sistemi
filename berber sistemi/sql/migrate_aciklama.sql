ALTER TABLE hizmetler ADD COLUMN aciklama TEXT NULL AFTER fiyat;

UPDATE hizmetler SET aciklama = 'Yüz şeklinize özel tasarlanmış, modern tekniklerle profesyonel saç kesimi hizmeti.' WHERE ad = 'Saç Kesimi';
UPDATE hizmetler SET aciklama = 'Düzenli hat çalışması ve özel bakım ürünleriyle tam bakımlı, temiz bir görünüm.' WHERE ad = 'Sakal Düzeltme';
UPDATE hizmetler SET aciklama = 'Saç kesimi ve sakal bakımını bir arada sunan komple erkek bakım paketi.' WHERE ad = 'Saç + Sakal';
UPDATE hizmetler SET aciklama = 'Kalıcı veya geçici renk seçenekleriyle istediğiniz tona ve görünüme kavuşun.' WHERE ad = 'Saç Boyama';
UPDATE hizmetler SET aciklama = 'Derin temizlik, gözenek sıkılaştırma ve besleyici maskelerle sağlıklı cilt.' WHERE ad = 'Cilt Bakımı';
