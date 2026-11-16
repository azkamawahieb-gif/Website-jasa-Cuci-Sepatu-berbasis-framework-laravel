// Halaman detail layanan: gambar sepatu dan daftar bagian saling terhubung.
(function () {
  var akar = document.querySelector('[data-bagian-akar]');
  if (!akar) return;

  var tombol = akar.querySelectorAll('[data-bagian]');
  var zona = akar.querySelectorAll('[data-zona]');
  var panel = akar.querySelectorAll('[data-panel]');
  if (!tombol.length) return;

  var kurangiGerak = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var kunci = [];
  tombol.forEach(function (b) { kunci.push(b.getAttribute('data-bagian')); });
  var sekarang = 0, pewaktu = null;

  function pilih(k) {
    tombol.forEach(function (b) {
      var on = b.getAttribute('data-bagian') === k;
      b.classList.toggle('aktif', on);
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    zona.forEach(function (z) { z.classList.toggle('aktif', z.getAttribute('data-zona') === k); });
    panel.forEach(function (p) { p.hidden = p.getAttribute('data-panel') !== k; });
    sekarang = kunci.indexOf(k);
  }

  function berhenti() {
    if (pewaktu) { clearInterval(pewaktu); pewaktu = null; }
  }
  function manual(k) { berhenti(); pilih(k); }

  tombol.forEach(function (b) {
    b.addEventListener('click', function () { manual(b.getAttribute('data-bagian')); });
  });

  zona.forEach(function (z) {
    var k = z.getAttribute('data-zona');
    z.addEventListener('click', function () { manual(k); });
    z.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); manual(k); }
    });
  });

  akar.addEventListener('mouseenter', berhenti);
  pilih(kunci[0]);

  // Sorotan berpindah otomatis sebagai petunjuk, berhenti begitu pengunjung menyentuhnya.
  if (!kurangiGerak && kunci.length > 1) {
    pewaktu = setInterval(function () {
      if (document.hidden) return;
      var r = akar.getBoundingClientRect();
      if (r.bottom < 0 || r.top > window.innerHeight) return;
      pilih(kunci[(sekarang + 1) % kunci.length]);
    }, 3500);
  }
})();