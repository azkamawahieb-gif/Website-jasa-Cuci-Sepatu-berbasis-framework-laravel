/* Chat: kirim pesan tanpa memuat ulang dan ambil pesan baru tiap 5 detik. Tanpa library. */
(function () {
  'use strict';

  var box = document.getElementById('chat');
  if (!box) return;

  var daftar = document.getElementById('chat-daftar');
  var form = document.getElementById('chat-form');
  var isi = document.getElementById('chat-isi');
  var galat = document.getElementById('chat-galat');
  var urlSejak = box.dataset.sejak;
  var terakhir = parseInt(box.dataset.terakhir, 10) || 0;
  var sedangKirim = false;

  function keBawah() { daftar.scrollTop = daftar.scrollHeight; }
  function dekatBawah() { return daftar.scrollHeight - daftar.scrollTop - daftar.clientHeight < 80; }

  function tampilGalat(teks) {
    galat.textContent = teks || '';
    galat.classList.toggle('chat-sembunyi', !teks);
  }

  function tambah(m) {
    if (daftar.querySelector('[data-id="' + m.id + '"]')) return;

    var kosong = document.getElementById('chat-kosong');
    if (kosong) kosong.remove();

    var g = document.createElement('div');
    g.className = 'chat-gelembung ' + (m.mine ? 'saya' : 'lawan');
    g.dataset.id = m.id;

    var p = document.createElement('p');
    p.textContent = m.body;
    var s = document.createElement('small');
    s.textContent = m.waktu;

    g.appendChild(p);
    g.appendChild(s);
    daftar.appendChild(g);

    if (m.id > terakhir) terakhir = m.id;
  }

  function ambil() {
    if (document.hidden) return;

    fetch(urlSejak + '?setelah=' + terakhir, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    }).then(function (res) {
      return res.ok ? res.json() : null;
    }).then(function (data) {
      if (!data || !data.pesan || !data.pesan.length) return;
      var turun = dekatBawah();
      data.pesan.forEach(tambah);
      if (turun) keBawah();
    }).catch(function () { /* diam: dicoba lagi 5 detik lagi */ });
  }

  setInterval(ambil, 5000);
  document.addEventListener('visibilitychange', function () { if (!document.hidden) ambil(); });

  function sesuaikanTinggi() {
    isi.style.height = 'auto';
    isi.style.height = Math.min(isi.scrollHeight, 120) + 'px';
  }
  isi.addEventListener('input', sesuaikanTinggi);

  // Enter = kirim, Shift+Enter = baris baru
  isi.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
      e.preventDefault();
      if (form.requestSubmit) form.requestSubmit();
      else form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (sedangKirim) return;
    if (isi.value.trim() === '') { tampilGalat('Tulis pesan dulu.'); return; }

    sedangKirim = true;
    tampilGalat('');

    fetch(form.action, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: new FormData(form),
      credentials: 'same-origin'
    }).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (data) { return { res: res, data: data }; });
    }).then(function (hasil) {
      if (hasil.res.status === 422) {
        var pesan = hasil.data.errors && hasil.data.errors.body ? hasil.data.errors.body[0] : 'Pesan belum bisa dikirim.';
        tampilGalat(pesan);
        return;
      }
      if (!hasil.res.ok || !hasil.data.pesan) {
        tampilGalat('Pesan belum terkirim. Muat ulang halaman lalu coba lagi.');
        return;
      }
      tambah(hasil.data.pesan);
      isi.value = '';
      sesuaikanTinggi();
      keBawah();
    }).catch(function () {
      tampilGalat('Tidak bisa terhubung ke server. Periksa koneksi lalu coba lagi.');
    }).then(function () {
      sedangKirim = false;
      isi.focus();
    });
  });

  keBawah();
})();