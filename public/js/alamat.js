/* Alamat: pratinjau Google Maps, tombol Kantor/Rumah, pemilih alamat di form pesanan,
   kotak "Tambah alamat baru" dan kotak "Ubah alamat". Tanpa library dan tanpa API key. */
(function () {
  'use strict';

  var MAPS = 'https://www.google.com/maps';

  function cariUrl(kata) {
    return MAPS + '/search/?api=1&query=' + encodeURIComponent(kata);
  }

  function koordinatValid(lat, lng) {
    return isFinite(lat) && isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180;
  }

  function bacaKoordinat(url) {
    var pola = [
      /@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/,
      /[?&](?:q|query|ll|destination)=(-?\d+(?:\.\d+)?)(?:,|%2C)(-?\d+(?:\.\d+)?)/,
      /!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/
    ];
    for (var i = 0; i < pola.length; i++) {
      var m = url.match(pola[i]);
      if (m && koordinatValid(parseFloat(m[1]), parseFloat(m[2]))) {
        return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
      }
    }
    return null;
  }

  /* ---------- Titik lokasi pada form alamat ---------- */
  function pasangPeta(blok) {
    var form = blok.closest('form');
    var lat = blok.querySelector('[data-lat]');
    var lng = blok.querySelector('[data-lng]');
    var url = blok.querySelector('[data-url]');
    var status = blok.querySelector('[data-status]');
    var preview = blok.querySelector('[data-preview]');
    var frame = blok.querySelector('[data-frame]');
    var tombolLokasi = blok.querySelector('[data-lokasi]');
    var tombolCari = blok.querySelector('[data-cari]');
    if (!lat || !lng || !url || !status || !preview || !frame || !tombolLokasi || !tombolCari) return;

    function pesan(teks) { status.textContent = teks || ''; }

    function tampil() {
      var a = parseFloat(lat.value);
      var b = parseFloat(lng.value);
      if (koordinatValid(a, b) && lat.value !== '' && lng.value !== '') {
        var src = MAPS + '?q=' + a + ',' + b + '&z=17&output=embed';
        if (frame.getAttribute('src') !== src) frame.setAttribute('src', src);
        preview.classList.remove('sembunyi');
      } else {
        frame.removeAttribute('src');
        preview.classList.add('sembunyi');
      }
    }

    blok._tampil = function () { pesan(''); tampil(); };

    tombolCari.addEventListener('click', function () {
      var kata = ['address', 'district', 'regency', 'province'].map(function (n) {
        return form.elements[n] ? form.elements[n].value.trim() : '';
      }).filter(Boolean).join(', ');
      if (!kata) { pesan('Isi alamat dulu supaya bisa dicari di Google Maps.'); return; }
      window.open(cariUrl(kata), '_blank', 'noopener');
      pesan('Di Google Maps, pilih lokasinya, tekan Bagikan, salin tautan, lalu tempel di kolom tautan.');
    });

    tombolLokasi.addEventListener('click', function () {
      if (!navigator.geolocation) { pesan('Browser ini tidak mendukung lokasi. Tempel tautan Google Maps saja.'); return; }
      pesan('Mengambil lokasi...');
      navigator.geolocation.getCurrentPosition(function (pos) {
        lat.value = pos.coords.latitude.toFixed(7);
        lng.value = pos.coords.longitude.toFixed(7);
        url.value = MAPS + '?q=' + lat.value + ',' + lng.value;
        tampil();
        pesan('Lokasi terpasang. Periksa letak titik di peta.');
      }, function () {
        pesan('Lokasi tidak bisa dibaca (izin ditolak, atau situs belum memakai https). Tempel tautan Google Maps saja.');
      }, { enableHighAccuracy: true, timeout: 10000 });
    });

    url.addEventListener('input', function () {
      var nilai = url.value.trim();
      if (nilai === '') { lat.value = ''; lng.value = ''; tampil(); pesan(''); return; }
      var k = bacaKoordinat(nilai);
      if (k) {
        lat.value = k.lat; lng.value = k.lng; tampil();
        pesan('Titik lokasi terbaca dari tautan.');
      } else {
        pesan('Tautan disimpan. Titik belum bisa ditampilkan dari tautan pendek, tetapi petugas tetap bisa membukanya.');
      }
    });

    if (form) form.addEventListener('reset', function () { setTimeout(function () { tampil(); pesan(''); }, 0); });
    tampil();
  }

  // Hanya blok titik lokasi pada form (bukan radio alamat yang juga membawa data peta).
  document.querySelectorAll('fieldset[data-peta]').forEach(pasangPeta);

  /* ---------- Tombol Kantor / Rumah ----------
     Bisa dipilih dengan menekan, dan dibatalkan dengan menekan lagi. Semua ditangani di sini
     (bukan oleh label bawaan browser) supaya pasti bisa ditekan di semua form alamat. */
  document.addEventListener('click', function (e) {
    var chip = e.target.closest ? e.target.closest('.pilih-chip') : null;
    if (!chip) return;
    // Klik asal keyboard pada radio sudah dipilih oleh browser sendiri.
    if (e.target.tagName === 'INPUT') return;

    e.preventDefault();
    var radio = chip.querySelector('input[type="radio"]');
    if (!radio || radio.disabled) return;
    radio.checked = !radio.checked;
    radio.dispatchEvent(new Event('change', { bubbles: true }));
  });

  /* ---------- Pemilih alamat di form pesanan ---------- */
  var kotak = document.querySelectorAll('[data-pilih]');

  function terapkan(box, awal) {
    var area = box.querySelector('[data-teks-alamat]');
    var peta = box.querySelector('[data-teks-peta]');
    var terpilih = box.querySelector('input[type="radio"]:checked');
    if (!terpilih || !area || !peta) return;

    var manual = terpilih.value === 'manual';
    var sebelumnya = box.dataset.mode || '';
    box.dataset.mode = manual ? 'manual' : 'tersimpan';

    area.readOnly = !manual;
    peta.readOnly = !manual;

    if (!manual) {
      area.value = terpilih.dataset.teks || '';
      peta.value = terpilih.dataset.tautan || '';
    } else if (!awal && sebelumnya === 'tersimpan') {
      area.value = '';
      peta.value = '';
      area.focus();
    }
  }

  kotak.forEach(function (box) {
    box.addEventListener('change', function (e) {
      if (e.target.matches('input[type="radio"]')) terapkan(box, false);
    });

    var cari = box.querySelector('[data-cari]');
    var area = box.querySelector('[data-teks-alamat]');
    if (cari && area) {
      cari.addEventListener('click', function () {
        var kata = area.value.trim();
        if (!kata) { alert('Isi alamat dulu supaya bisa dicari di Google Maps.'); return; }
        window.open(cariUrl(kata), '_blank', 'noopener');
      });
    }

    terapkan(box, true);
  });

  /* ---------- Kotak "Tambah alamat baru" dan "Ubah alamat" ---------- */
  var dlgTambah = document.getElementById('dlg-alamat');
  var dlgUbah = document.getElementById('dlg-ubah-alamat');
  if (!dlgTambah || typeof dlgTambah.showModal !== 'function') return;

  function el(tag, kelas, teks) {
    var e = document.createElement(tag);
    if (kelas) e.className = kelas;
    if (teks !== undefined) e.textContent = teks;
    return e;
  }

  function siapkan(dlg) {
    var form = dlg.querySelector('form[data-ajax-alamat]');
    var galat = dlg.querySelector('[data-galat]');

    function tutup() { if (dlg.open) dlg.close(); }

    dlg.querySelectorAll('[data-tutup]').forEach(function (t) { t.addEventListener('click', tutup); });
    dlg.addEventListener('click', function (e) { if (e.target === dlg) tutup(); });

    function tampilkanGalat(daftar) {
      galat.textContent = '';
      var ul = el('ul');
      ul.style.marginLeft = '1.2rem';
      daftar.forEach(function (t) { ul.appendChild(el('li', '', t)); });
      galat.appendChild(ul);
      galat.classList.remove('sembunyi');
      galat.scrollIntoView({ block: 'nearest' });
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var kirim = form.querySelector('button[type="submit"]');
      kirim.disabled = true;

      fetch(form.action, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
        credentials: 'same-origin'
      }).then(function (res) {
        return res.json().catch(function () { return {}; }).then(function (data) { return { res: res, data: data }; });
      }).then(function (hasil) {
        if (hasil.res.status === 422) {
          var pesanan = [];
          Object.keys(hasil.data.errors || {}).forEach(function (k) { pesanan = pesanan.concat(hasil.data.errors[k]); });
          tampilkanGalat(pesanan.length ? pesanan : ['Data alamat belum lengkap.']);
          return;
        }
        if (!hasil.res.ok || !hasil.data.alamat) {
          tampilkanGalat(['Alamat belum bisa disimpan. Muat ulang halaman lalu coba lagi.']);
          return;
        }
        dlg._berhasil(hasil.data.alamat);
        form.reset();
        tutup();
      }).catch(function () {
        tampilkanGalat(['Tidak bisa terhubung ke server. Periksa koneksi lalu coba lagi.']);
      }).then(function () {
        kirim.disabled = false;
      });
    });

    return { form: form, galat: galat };
  }

  // Satu pilihan alamat di daftar (dibuat ulang setelah tambah / ubah)
  function buatOpsi(tipe, a) {
    var label = el('label', 'alamat-opsi');
    var radio = document.createElement('input');
    radio.type = 'radio';
    radio.name = tipe + '_pilih';
    radio.value = a.id;
    radio.dataset.teks = a.lengkap;
    radio.dataset.tautan = a.peta || '';
    radio.dataset.json = JSON.stringify(a);

    var isi = el('span', 'alamat-isi');
    var nama = el('span', 'alamat-nama');
    nama.appendChild(el('strong', '', a.recipient_name));
    nama.appendChild(el('span', 'muted', '| ' + a.phone));
    if (a.is_default) nama.appendChild(el('span', 'alamat-lencana badge-utama', 'Utama'));
    if (a.label) nama.appendChild(el('span', 'alamat-lencana', a.label));
    isi.appendChild(nama);
    isi.appendChild(el('span', '', a.address));
    if (a.wilayah) isi.appendChild(el('span', 'muted', a.wilayah));

    var ubah = el('button', 'alamat-ubah-btn', 'Ubah');
    ubah.type = 'button';
    ubah.setAttribute('data-ubah-alamat', a.id);

    label.appendChild(radio);
    label.appendChild(isi);
    label.appendChild(ubah);
    return label;
  }

  /* -- Tambah -- */
  var tambah = siapkan(dlgTambah);
  var target = null;

  document.querySelectorAll('[data-tambah-alamat]').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
      target = tombol.closest('[data-pilih]');
      tambah.galat.classList.add('sembunyi');
      dlgTambah.showModal();
    });
  });

  dlgTambah._berhasil = function (a) {
    kotak.forEach(function (box) {
      var tipe = box.dataset.pilih;
      if (a.is_default) box.querySelectorAll('.badge-utama').forEach(function (b) { b.remove(); });

      var opsi = buatOpsi(tipe, a);
      var manual = box.querySelector('[data-opsi-manual]');
      manual.parentNode.insertBefore(opsi, manual);

      if (box === target) {
        opsi.querySelector('input').checked = true;
        terapkan(box, true);
      }
    });
  };

  /* -- Ubah -- */
  if (!dlgUbah) return;

  var ubah = siapkan(dlgUbah);
  var aksiUbah = dlgUbah.dataset.aksi || '';

  function isiForm(f, a) {
    ['province', 'regency', 'district', 'postal_code', 'address', 'detail',
     'recipient_name', 'phone', 'maps_url', 'latitude', 'longitude'].forEach(function (n) {
      var e = f.elements[n];
      if (e) e.value = a[n] == null ? '' : a[n];
    });

    if (f.elements['is_default']) f.elements['is_default'].checked = !!a.is_default;

    f.querySelectorAll('input[name="label"]').forEach(function (r) { r.checked = (r.value === a.label); });

    var blok = f.querySelector('fieldset[data-peta]');
    if (blok && blok._tampil) blok._tampil();
  }

  document.addEventListener('click', function (e) {
    var tombol = e.target.closest ? e.target.closest('[data-ubah-alamat]') : null;
    if (!tombol) return;

    e.preventDefault();
    var radio = tombol.closest('.alamat-opsi').querySelector('input[type="radio"]');
    var data;
    try { data = JSON.parse(radio.dataset.json || ''); } catch (x) { data = null; }
    if (!data) return;

    ubah.galat.classList.add('sembunyi');
    ubah.form.action = aksiUbah.replace('__ID__', data.id);
    isiForm(ubah.form, data);
    dlgUbah.showModal();
  });

  dlgUbah._berhasil = function (a) {
    kotak.forEach(function (box) {
      var tipe = box.dataset.pilih;
      if (a.is_default) box.querySelectorAll('.badge-utama').forEach(function (b) { b.remove(); });

      var lama = box.querySelector('input[type="radio"][value="' + a.id + '"]');
      if (!lama) return;

      var label = lama.closest('.alamat-opsi');
      var dipilih = lama.checked;
      var baru = buatOpsi(tipe, a);
      label.parentNode.replaceChild(baru, label);

      if (dipilih) {
        baru.querySelector('input').checked = true;
        terapkan(box, true);
      }
    });
  };
})();